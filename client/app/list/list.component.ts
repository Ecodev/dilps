import { forkJoin } from 'rxjs';
import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CardService } from '../card/services/card.service';
import { clone, defaults, isArray, isString, merge, pickBy } from 'lodash';
import { DownloadComponent } from '../shared/components/download/download.component';
import { CardFilter, CardSortingField } from '../shared/generated-types';
import { cardsConfiguration } from '../shared/natural-search-configurations';
import { PerfectScrollbarComponent } from 'ngx-perfect-scrollbar';
import { MatDialog } from '@angular/material';
import { CollectionSelectorComponent } from '../shared/components/collection-selector/collection-selector.component';
import { CollectionService } from '../collections/services/collection.service';
import { AlertService } from '../shared/components/alert/alert.service';
import { UserService } from '../users/services/user.service';
import { UtilityService } from '../shared/services/utility.service';
import { NumberSelectorComponent } from '../quizz/shared/number-selector/number-selector.component';
import { MassEditComponent } from '../shared/components/mass-edit/mass-edit.component';

import { NaturalGalleryComponent } from '@ecodev/angular-natural-gallery';
import { NaturalSearchConfiguration, NaturalSearchSelections, toGraphQLDoctrineFilter } from '@ecodev/natural-search';
import { FilterManager } from '../shared/classes/filter-manager';

@Component({
    selector: 'app-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
})
export class ListComponent implements OnInit {

    @ViewChild('gallery') gallery: NaturalGalleryComponent;
    @ViewChild('scrollable') private scrollable: PerfectScrollbarComponent;

    public galleryCollection = null;
    public options = null;
    public selected;

    public showLogo = true;

    private thumbnailHeight = 400;
    private enlargedHeight = 2000;
    private sub;

    private firstPagination;

    public collection;

    public user;

    public searchedTerm;

    public lastUpload;

    public showDownloadCollection = true;

    public config: NaturalSearchConfiguration = cardsConfiguration;

    public graphqlFilter;
    public selections: NaturalSearchSelections = [
        [],
    ];

    private filterManager: FilterManager = new FilterManager();

    constructor(private router: Router,
                private route: ActivatedRoute,
                private cardSvc: CardService,
                private dialog: MatDialog,
                private collectionSvc: CollectionService,
                private alertSvc: AlertService,
                private userSvc: UserService) {
    }

    ngOnInit() {

        this.userSvc.getCurrentUser().subscribe(user => {
            this.user = user;
            this.updateShowDownloadCollection();
        });

        this.route.params.subscribe(params => {

            if (params.upload && params.upload !== this.lastUpload) {
                this.reload();
            }

            if (params.collectionId) {
                this.collection = {
                    id: params.collectionId,
                    __typename: 'Collection',
                };
                this.galleryCollection = [];
                this.filterManager.set('collection', {
                    filter: {
                        conditions: [
                            {fields: {collections: {have: {values: [params.collectionId]}}}},
                        ],
                    },
                });
            }
        });

        this.route.data.subscribe(data => {

            this.showLogo = data.showLogo;
            this.updateShowDownloadCollection();

            const filters: CardFilter = {
                conditions: [
                    {
                        fields: {
                            filename: {
                                equal: {
                                    value: '',
                                    not: true,
                                },
                            },
                        },
                    },
                ],
            };

            if (data.filter) {
                this.filterManager.set('route-context', {filter : data.filter});
            }

            if (data.creator && !this.collection) {
                filters.conditions[filters.conditions.length - 1].fields.creator = {equal: {value: data.creator.id}};
            }

            this.galleryCollection = [];
            this.filterManager.set('controller-variables', {filter: filters});
        });

        this.options = {
            margin: 5,
            showLabels: 'true',
            rowHeight: this.thumbnailHeight,
            zoomRotation: false,
        };

    }

    public updateShowDownloadCollection() {
        const roles = this.route.snapshot.data.showDownloadCollectionForRoles;
        const roleIsAllowed = this.user && this.user.role && (!roles || roles && roles.indexOf(this.user.role) > -1);
        const hasCollection = this.collection && this.collection.id;
        this.showDownloadCollection = hasCollection && roleIsAllowed;
    }

    private formatImages(cards) {

        cards = cards.map(card => {
            let thumb = CardService.formatImage(card, this.thumbnailHeight);
            let big = CardService.formatImage(card, this.enlargedHeight);

            thumb = {
                thumbnail: thumb.src,
                tWidth: thumb.width,
                tHeight: thumb.height,
            };

            big = {
                enlarged: big.src,
                eWidth: big.width,
                eHeight: big.height,
            };

            let title = card.name ? card.name : null;

            card.artists.forEach(artist => {
                title += ', ' + artist.name;
            });

            const fields: any = {
                title: title ? title : 'Voir le détail',
            };

            if (this.user) {
                fields.link = 'true';
            }

            return merge({}, card, thumb, big, fields);
        });

        return cards;
    }

    public activate(item) {
        this.router.navigate([
            'card',
            item.id,
        ]);
    }

    public select(items) {
        this.selected = items;
    }

    public reload() {
        if (this.sub) {
            this.selected = [];
            this.gallery.collection = [];
            this.filterManager.set('pagination', this.firstPagination);
            this.sub.refetch();
        }
    }

    public search(term: NaturalSearchSelections) {
        const filter = toGraphQLDoctrineFilter(this.config, term);
        this.graphqlFilter = filter;
        this.reload();
        this.filterManager.set('search', {filter: filter});
    }

    public loadMore(ev) {

        const pagination = {
            pagination: {
                offset: ev.offset,
                pageSize: ev.limit,
            },
        };

        if (!this.firstPagination) {
            this.firstPagination = pagination;
        }

        this.filterManager.set('pagination', pagination);

        if (!this.sub) {
            this.sub = this.cardSvc.watchAll(this.filterManager.filters);
            this.sub.valueChanges.subscribe(data => {
                this.gallery.gallery.addItems(this.formatImages(data.items));
            });
        }
    }

    private linkToCollection(selection) {
        this.dialog.open(CollectionSelectorComponent, {
            width: '400px',
            position: {
                top: '74px',
                right: '10px',
            },
            data: selection,
        });
    }

    public linkSelectionToCollection(selection) {
        this.linkToCollection({images: selection});
    }

    public linkCollectionToCollection(collection) {
        this.linkToCollection({collection});
    }

    private download(selection) {
        const data = merge({denyLegendsDownload: !this.user}, selection);

        this.dialog.open(DownloadComponent, {
            width: '400px',
            data,
        });
    }

    public downloadSelection(selection) {
        this.download({images: selection});
    }

    public downloadCollection(collection) {
        this.download({collection});
    }

    public unlinkFromCollection(selection) {

        if (!this.collection) {
            return;
        }

        this.collectionSvc.unlink(this.collection, selection).subscribe(() => {
            this.alertSvc.info('Les images ont été retirées');
            this.reload();
        });
    }

    public delete(selection) {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet/ces élément(s) ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.cardSvc.delete(selection).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.reload();
                    });
                }
            });
    }

    public goToQuizz(selected = null) {

        if (selected) {
            selected = UtilityService.shuffleArray(selected.map(e => e.id)).join(',');
            this.router.navigateByUrl('/quizz;cards=' + selected);
        } else {
            // open box, ask for number of items to display in quizz, and get randomized list pageIndex:0, pageSize:nbItems; sort: random'
            this.dialog.open(NumberSelectorComponent, {
                width: '200px',
                position: {
                    top: '74px',
                    right: '10px',
                },
            }).afterClosed().subscribe(number => {
                if (number > 0) {
                    const quizzVars = clone(this.filterManager.filters.value);
                    quizzVars.sorting = [{field: CardSortingField.random}];
                    quizzVars.pagination.pageIndex = 0;
                    quizzVars.pagination.pageSize = +number;
                    this.cardSvc.getAll(quizzVars).subscribe(cards => {
                        this.router.navigateByUrl('quizz;cards=' + cards.items.map(e => e.id).join(','));
                    });
                }
            });
        }
    }

    public edit(selected) {
        const selection = selected.filter(card => card.permissions.update);

        this.dialog.open(MassEditComponent, {
            width: '440px',
        }).afterClosed().subscribe(model => {

            if (!model) {
                return;
            }

            const changeAttributes = pickBy(model, (value, key) => {
                return isString(value) && value !== '' || isArray(value) && value.length > 0;
            });

            const observables = [];
            for (const s of selection) {
                const changes = clone(changeAttributes);
                defaults(changes, s);
                if (changes.artists) {
                    changes.artists = changes.artists.map(a => a.name ? a.name : a);
                }
                if (changes.institution) {
                    changes.institution = changes.institution.name ? changes.institution.name : changes.institution;
                }
                observables.push(this.cardSvc.update(changes as { id: any }));
            }

            forkJoin(observables).subscribe(() => {
                this.alertSvc.info('Mis à jour');
                this.reload();
            });
        });

    }
}

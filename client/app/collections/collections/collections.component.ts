import { Component, OnInit } from '@angular/core';
import { CollectionService } from '../services/collection.service';
import { ActivatedRoute, Router } from '@angular/router';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { MatDialog } from '@angular/material';
import { CollectionComponent } from '../collection/collection.component';
import { Literal } from '../../shared/types';
import { CollectionVisibility, UserRole } from '../../shared/generated-types';
import { UserService } from '../../users/services/user.service';

@Component({
    selector: 'app-collections',
    templateUrl: './collections.component.html',
    styleUrls: ['./collections.component.scss'],
})
export class CollectionsComponent implements OnInit {

    public collections = [];
    private queryVariables = new IncrementSubject({});

    /**
     * Show "unclassified" category on the top of the page
     * @type {boolean}
     */
    public showUnclassified = false;
    /**
     * Show "my cards" category on the top of the page
     * @type {boolean}
     */
    public showMyCards = false;

    /**
     * Can create permissions
     */
    public canCreate = false;

    public searchedTerm;

    private pageSize = 50;

    private defaultFilters = {
        filters: {
            search: '',
            parents: [],
        },
        pagination: {
            pageIndex: 0,
            pageSize: this.pageSize,
        },
    };

    public hasMore = false;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private collectionsSvc: CollectionService,
                private dialog: MatDialog,
                private userSvc: UserService) {
    }

    ngOnInit() {

        this.queryVariables.patch(this.defaultFilters);
        this.route.data.subscribe((data: Literal) => {
            this.canCreate = !!data.canCreate;
            this.showUnclassified = data.showUnclassified;
            this.showMyCards = data.showMyCards;

            const filters = data.filters ? data.filters : {};

            if (data.creator) {
                filters.creators = [data.creator.id];
            }

            this.queryVariables.patch({filters: filters});
        });

        const queryRef = this.collectionsSvc.watchAll(this.queryVariables);
        queryRef.valueChanges.subscribe((collections: any) => {
            if (collections.pageIndex === 0) {
                this.collections = collections.items;
            } else {
                this.collections = this.collections.concat(collections.items);
            }
            this.hasMore = collections.length > this.collections.length;
        });

    }

    public search(term) {
        this.queryVariables.patch({filters: {search: term}});
    }

    public more() {
        const nextPage = this.queryVariables.getValue().pagination.pageIndex + 1;
        this.queryVariables.patch({pagination: {pageIndex: nextPage}});
    }

    public edit(event, collection) {
        event.preventDefault();
        event.stopPropagation();

        const dialogRef = this.dialog.open(CollectionComponent, {
            width: '800px',
            data: {item: collection},
        });

        dialogRef.afterClosed().subscribe(data => {
            // if returned data is null, it means deletion
            if (data === null) {
                this.router.navigate(['..'], {relativeTo: this.route.firstChild});
            }
        });
    }

    public add() {
        const dialogRef = this.dialog.open(CollectionComponent, {
            width: '800px',
        });

        dialogRef.afterClosed().subscribe(collection => {
            if (collection) {
                this.router.navigate([
                    '/collection',
                    collection.id,
                ], {relativeTo: this.route.firstChild});

            }
        });
    }

}

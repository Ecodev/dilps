import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CardService } from '../card/services/card.service';
import { merge } from 'lodash';
import { IncrementSubject } from '../shared/services/increment-subject';
import { PerfectScrollbarComponent } from 'ngx-perfect-scrollbar';

@Component({
    selector: 'app-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
})
export class ListComponent implements OnInit {

    @ViewChild('gallery') gallery;
    @ViewChild('scrollable') private scrollable: PerfectScrollbarComponent;

    public galleryCollection = null;
    public options = null;
    public selected;

    public showLogo = true;

    private thumbnailHeight = 400;
    private enlargedHeight = 2000;
    private sub;

    private queryVariables = new IncrementSubject({});

    constructor(private router: Router, private route: ActivatedRoute, private cardSvc: CardService) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => this.showLogo = data.showLogo);

        this.route.params.subscribe(params => {
            if (params.collectionId) {
                this.galleryCollection = [];
                this.queryVariables.patch({filters: {collections: [params.collectionId]}});
            }
        });

        this.options = {
            margin: 5,
            showLabels: 'true',
            rowHeight: this.thumbnailHeight,
        };

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

            const fields = {
                link: 'true',
                title: card.name ? card.name : 'Voir le détail',
            };

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

    public loadMore(ev) {

        this.queryVariables.patch({
            pagination: {
                offset: ev.offset,
                pageSize: ev.limit,
            },
        });

        if (!this.sub) {
            this.sub = this.cardSvc.watchAll(this.queryVariables);
            this.sub.valueChanges.subscribe(data => {
                this.gallery.addItems(this.formatImages(data.items));
            });
        }
    }

}

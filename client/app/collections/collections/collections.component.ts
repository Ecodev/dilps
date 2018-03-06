import { Component, OnInit } from '@angular/core';
import { CollectionService } from '../services/collection.service';
import { ActivatedRoute, Router } from '@angular/router';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { MatDialog } from '@angular/material';
import { CollectionComponent } from '../collection/collection.component';
import { Literal } from '../../shared/types';

@Component({
    selector: 'app-collections',
    templateUrl: './collections.component.html',
    styleUrls: ['./collections.component.scss'],
})
export class CollectionsComponent implements OnInit {

    public collections;
    private queryVariables = new IncrementSubject({});
    /**
     * Show descriptions
     * @type {boolean}
     */
    public showDescs = false;

    /**
     * Show "unclassified" category on the top of the page
     * @type {boolean}
     */
    public showUnclassified = false;

    /**
     * Can create permissions
     */
    public canCreate = false;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private collectionsSvc: CollectionService,
                private dialog: MatDialog) {
    }

    ngOnInit() {

        const queryRef = this.collectionsSvc.watchAll(this.queryVariables);

        this.route.data.subscribe((data: Literal) => {
            this.canCreate = !!data.canCreate;
            this.showUnclassified = data.showUnclassified;

            const filters = data.filters ? data.filters : {};

            if (data.creator) {
                filters.creators = [data.creator.id];
            }

            this.queryVariables.patch({filters: filters});
        });

        queryRef.valueChanges.subscribe(collections => {
            this.collections = collections;
        });
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

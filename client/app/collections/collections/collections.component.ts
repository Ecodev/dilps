import { Component, OnInit } from '@angular/core';
import { CollectionService } from '../services/collection.service';
import { ActivatedRoute, Router } from '@angular/router';
import { merge } from 'lodash';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { MatDialog } from '@angular/material';
import { CollectionComponent } from '../collection/collection.component';

@Component({
    selector: 'app-collections',
    templateUrl: './collections.component.html',
    styleUrls: ['./collections.component.scss'],
})
export class CollectionsComponent implements OnInit {

    public collections;

    public showDescs;
    private queryVariables = new IncrementSubject({});

    constructor(private route: ActivatedRoute,
        private router: Router,
        private collectionsSvc: CollectionService,
        private dialog: MatDialog) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => {
            this.queryVariables.patch({filter: data.filter});
        });
        this.collectionsSvc.watchAll(this.queryVariables).subscribe(collections => this.collections = collections.items);
    }

    public edit(collection) {
        const dialogRef = this.dialog.open(CollectionComponent, {
            width: '500px',
            data: {collection: collection},
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
            width: '500px',
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

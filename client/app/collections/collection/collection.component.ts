import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionService } from '../services/collection.service';
import { merge } from 'lodash';
import { AlertService } from '../../shared/services/alert.service';

@Component({
    selector: 'app-collection',
    templateUrl: './collection.component.html',
    styleUrls: ['./collection.component.scss'],
})
export class CollectionComponent implements OnInit {

    public collection: any = {
        description: '',
        isSource: false,
        sorting: 0,
    };

    constructor(@Inject(MAT_DIALOG_DATA) public data: any,
        public dialogRef: MatDialogRef<CollectionComponent>,
        private collectionSvc: CollectionService,
        private alertSvc: AlertService) {
    }

    ngOnInit() {
        if (this.data && this.data.collection) {
            merge(this.collection, this.data.collection);
            this.collectionSvc.getOne(this.data.collection.id).subscribe(collection => {
                merge(this.collection, collection);
            });
        }
    }

    public update() {
        this.collectionSvc.update(this.collection).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.collectionSvc.create(this.collection).subscribe(collection => {
            this.collection.id = collection.id;
            this.alertSvc.info('Créé');
            this.dialogRef.close(this.collection);
        });
    }

    public delete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.collectionSvc.delete(this.collection).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.dialogRef.close(null);
                    });
                }
            });
    }

}

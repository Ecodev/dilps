import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionComponent } from '../../collections/collection/collection.component';
import { AlertService } from '../../shared/components/alert/alert.service';
import { merge } from 'lodash';
import { InstitutionService } from '../services/institution.service';

@Component({
    selector: 'app-institution',
    templateUrl: './institution.component.html',
})
export class InstitutionComponent implements OnInit {

    public institution: any = {};

    constructor(public institutionService: InstitutionService,
                private alertSvc: AlertService,
                public dialogRef: MatDialogRef<CollectionComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit() {
        if (this.data && this.data.institution) {
            merge(this.institution, this.data.institution); // init available field asap (from listing)
            this.institutionService.getOne(this.data.institution.id).subscribe(institution => {
                merge(this.institution, institution); // init all fields considering getOne query
            });
        }
    }

    public update() {
        this.institutionService.update(this.institution).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.institutionService.create(this.institution).subscribe(institution => {
            this.institution.id = institution.id;
            this.alertSvc.info('Créé');
            this.dialogRef.close(this.institution);
        });
    }

    public delete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.institutionService.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.dialogRef.close(null);
                    });
                }
            });
    }
}

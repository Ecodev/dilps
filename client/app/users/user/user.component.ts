import { Component, Inject, OnInit } from '@angular/core';
import { ThemeService } from '../../shared/services/theme.service';
import { UserService } from '../services/user.service';
import { InstitutionService } from '../../institutions/services/institution.service';
import { AlertService } from '../../shared/components/alert/alert.service';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionComponent } from '../../collections/collection/collection.component';
import { merge } from 'lodash';

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
    styleUrls: ['./user.component.scss'],
})
export class UserComponent implements OnInit {

    public user: any = {
        type: 'default',
        institution: null,
    };

    public theme: string;

    constructor(public themeSvc: ThemeService,
                public institutionSvc: InstitutionService,
                private userSvc: UserService,
                private alertSvc: AlertService,
                public dialogRef: MatDialogRef<CollectionComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit() {
        if (this.data && this.data.user) {
            merge(this.user, this.data.user);
            this.userSvc.getOne(this.data.user.id).subscribe(user => {
                merge(this.user, user);
            });
        }
    }

    public update() {
        this.userSvc.update(this.user).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.userSvc.create(this.user).subscribe(user => {
            this.user.id = user.id;
            this.alertSvc.info('Créé');
            this.dialogRef.close(this.user);
        });
    }

    public delete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.userSvc.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.dialogRef.close(null);
                    });
                }
            });
    }

}

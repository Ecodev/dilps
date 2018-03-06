import { Component, Inject } from '@angular/core';
import { AlertService } from '../../shared/components/alert/alert.service';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { AbstractDetail } from '../../shared/components/AbstractDetail';
import { ArtistComponent } from '../../artists/artist/artist.component';
import { InstitutionService } from '../../institutions/services/institution.service';
import { UserService } from '../services/user.service';

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
})
export class UserComponent extends AbstractDetail {

    public roles = [];

    constructor(public institutionService: InstitutionService,
                service: UserService,
                alertSvc: AlertService,
                userSvc: UserService,
                dialogRef: MatDialogRef<ArtistComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, userSvc, data);

        this.roles = service.getRoles();
    }

}

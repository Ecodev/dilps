import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionService } from '../services/collection.service';
import { AlertService } from '../../shared/components/alert/alert.service';
import { InstitutionService } from '../../institutions/services/institution.service';
import { AbstractDetail } from '../../shared/components/AbstractDetail';
import { UserService } from '../../users/services/user.service';

@Component({
    selector: 'app-collection',
    templateUrl: './collection.component.html',
})
export class CollectionComponent extends AbstractDetail {

    constructor(public institutionSvc: InstitutionService,
                service: CollectionService,
                userSvc: UserService,
                alertSvc: AlertService,
                dialogRef: MatDialogRef<CollectionComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, userSvc, data);
    }
}

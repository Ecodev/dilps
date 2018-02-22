import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { AbstractDetail } from '../../shared/components/AbstractDetail';
import { AlertService } from '../../shared/components/alert/alert.service';
import { InstitutionService } from '../services/institution.service';

@Component({
    selector: 'app-institution',
    templateUrl: './institution.component.html',
})
export class InstitutionComponent extends AbstractDetail {

    constructor(service: InstitutionService,
                alertSvc: AlertService,
                dialogRef: MatDialogRef<InstitutionComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, data);
    }
}

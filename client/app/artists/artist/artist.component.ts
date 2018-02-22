import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { AlertService } from '../../shared/components/alert/alert.service';
import { ArtistService } from '../services/artist.service';
import { AbstractDetail } from '../../shared/components/AbstractDetail';

@Component({
    selector: 'app-artist',
    templateUrl: './artist.component.html',
})
export class ArtistComponent extends AbstractDetail {

    constructor(service: ArtistService,
                alertSvc: AlertService,
                dialogRef: MatDialogRef<ArtistComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, data);
    }
}

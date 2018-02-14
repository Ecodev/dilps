import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionComponent } from '../../collections/collection/collection.component';
import { AlertService } from '../../shared/components/alert/alert.service';
import { merge } from 'lodash';
import { ArtistService } from '../services/artist.service';

@Component({
    selector: 'app-artist',
    templateUrl: './artist.component.html',
})
export class ArtistComponent implements OnInit {

    public artist: any = {};

    constructor(public artistService: ArtistService,
                private alertSvc: AlertService,
                public dialogRef: MatDialogRef<CollectionComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit() {
        if (this.data && this.data.artist) {
            merge(this.artist, this.data.artist); // init available field asap (from listing)
            this.artistService.getOne(this.data.artist.id).subscribe(artist => {
                merge(this.artist, artist); // init all fields considering getOne query
            });
        }
    }

    public update() {
        this.artistService.update(this.artist).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.artistService.create(this.artist).subscribe(artist => {
            this.artist.id = artist.id;
            this.alertSvc.info('Créé');
            this.dialogRef.close(this.artist);
        });
    }

    public delete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.artistService.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.dialogRef.close(null);
                    });
                }
            });
    }
}

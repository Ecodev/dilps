import { Component, Inject, OnInit } from '@angular/core';
import { CollectionService } from '../../../collections/services/collection.service';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { ArtistComponent } from '../../../artists/artist/artist.component';
import 'rxjs/add/observable/forkJoin';

@Component({
    selector: 'app-collection-selector',
    templateUrl: './collection-selector.component.html',
    styleUrls: ['./collection-selector.component.scss'],
})
export class CollectionSelectorComponent implements OnInit {

    public collection;
    public newCollection: any = {
        name: '',
        description: '',
        parent: null,
    };

    constructor(public collectionSvc: CollectionService,
                private dialogRef: MatDialogRef<ArtistComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit() {
    }

    public link() {
        this.collectionSvc.link(this.collection, this.data.images).subscribe(() => {
            this.dialogRef.close(this.collection);
        });
    }

    public createAndLink() {
        this.collectionSvc.create(this.newCollection).subscribe(collection => {
            this.collectionSvc.link(collection, this.data.images).subscribe(() => {
                this.dialogRef.close(collection);
            });
        });
    }
}

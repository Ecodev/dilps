import { Component, Inject, OnInit } from '@angular/core';
import { CollectionService } from '../../../collections/services/collection.service';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { ArtistComponent } from '../../../artists/artist/artist.component';
import 'rxjs/add/observable/forkJoin';
import { UserService } from '../../../users/services/user.service';
import { Literal } from '../../types';
import { UserRole, CollectionVisibility } from '../../generated-types';

@Component({
    selector: 'app-collection-selector',
    templateUrl: './collection-selector.component.html',
    styleUrls: ['./collection-selector.component.scss'],
})
export class CollectionSelectorComponent implements OnInit {

    public listFilters: Literal;
    public collection;
    public newCollection: any = {
        name: '',
        description: '',
        parent: null,
    };

    constructor(public collectionSvc: CollectionService,
                private dialogRef: MatDialogRef<ArtistComponent>,
                private userSvc: UserService,
                @Inject(MAT_DIALOG_DATA) public data: any) {
    }

    ngOnInit() {

        this.userSvc.getCurrentUser().subscribe(user => {
            if (user.role !== UserRole.administrator) {
                this.listFilters = {creators: [user.id]};
            }
        });
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

import { Component, OnInit } from '@angular/core';
import { CollectionService } from '../services/collection.service';
import { ActivatedRoute } from '@angular/router';
import { merge } from 'lodash';
import { IncrementSubject } from '../../shared/services/increment-subject';

@Component({
    selector: 'app-collections',
    templateUrl: './collections.component.html',
    styleUrls: ['./collections.component.scss'],
})
export class CollectionsComponent implements OnInit {

    public collections;

    private queryVariables = new IncrementSubject({});

    constructor(private route: ActivatedRoute, private collectionsSvc: CollectionService) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => {
            this.queryVariables.patch({filter: data.filter});
        });
        this.collectionsSvc.watchAll(this.queryVariables).subscribe(collections => this.collections = collections.items);
    }

}

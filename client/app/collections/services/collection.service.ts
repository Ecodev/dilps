import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    collectionQuery,
    collectionsQuery,
    createCollectionMutation,
    deleteCollectionsMutation,
    updateCollectionMutation,
} from './collectionQueries';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import {
    CollectionInput,
    CollectionQuery,
    CollectionsQuery,
    CreateCollectionMutation,
    DeleteCollectionsMutation,
    UpdateCollectionMutation,
    CollectionVisibility,
} from '../../shared/generated-types';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/observable/forkJoin';
import { LinkMutationService } from '../../shared/services/link-mutation.service';

@Injectable()
export class CollectionService
    extends AbstractModelService<CollectionQuery['collection'],
        CollectionsQuery['collections'],
        CreateCollectionMutation['createCollection'],
        UpdateCollectionMutation['updateCollection'],
        DeleteCollectionsMutation['deleteCollections']> {

    constructor(apollo: Apollo, private linkSvc: LinkMutationService) {
        super(apollo,
            'collection',
            collectionQuery,
            collectionsQuery,
            createCollectionMutation,
            updateCollectionMutation,
            deleteCollectionsMutation);
    }

    public getEmptyObject(): CollectionInput {
        return {
            name: '',
            description: '',
            isSource: false,
            sorting: 0,
            visibility: CollectionVisibility.private,
        };
    }

    public link(collection, images) {
        const observables = [];
        images.forEach(image => {
            observables.push(this.linkSvc.link(collection, image));
        });

        return Observable.forkJoin(observables);
    }

    public unlink(collection, images) {
        const observables = [];
        images.forEach(image => {
            observables.push(this.linkSvc.unlink(collection, image));
        });

        return Observable.forkJoin(observables);
    }

}

import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import {
    collectionQuery,
    collectionsQuery,
    createCollectionMutation,
    deleteCollectionMutation,
    updateCollectionMutation,
} from './collectionQueries';
import 'rxjs/add/observable/of';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import {
    CollectionQuery,
    CollectionsQuery,
    CreateCollectionMutation,
    DeleteCollectionMutation,
    UpdateCollectionMutation,
} from '../../shared/generated-types';

@Injectable()
export class CollectionService
    extends AbstractModelService<CollectionQuery['collection'],
        CollectionsQuery['collections'],
        CreateCollectionMutation['createCollection'],
        UpdateCollectionMutation['updateCollection'],
        DeleteCollectionMutation['deleteCollection']> {

    constructor(apollo: Apollo) {
        super(apollo,
            'collection',
            collectionQuery,
            collectionsQuery,
            createCollectionMutation,
            updateCollectionMutation,
            deleteCollectionMutation);
    }

    public getEmptyObject() {
        return {
            name: '',
            description: '',
            isSource: false,
            sorting: 0,
        };
    }

}

import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Observable } from 'rxjs/Observable';
import {
    collectionQuery, collectionsQuery, createCollectionMutation, deleteCollectionMutation, updateCollectionMutation,
} from '../services/collection';
import 'rxjs/add/observable/of';
import { filter, map } from 'rxjs/operators';
import { merge, omit } from 'lodash';

@Injectable()
export class CollectionService {

    constructor(private apollo: Apollo) {
    }

    public watchAll(variables): Observable<any> {

        const query = this
            .apollo
            .watchQuery({
                query: collectionsQuery,
                variables: variables.getValue(),
                fetchPolicy: 'cache-and-network',
            });

        variables.subscribe(data => {
            query.setVariables(data);
        });

        return query
            .valueChanges
            .pipe(filter((data: any) => !!data.data && !data.loading), map((data: any) => {
                return data.data.collections;
            }));
    }

    public getOne(id): Observable<any> {
        return this
            .apollo
            .query({
                query: collectionQuery,
                variables: {
                    id: id,
                },
            })
            .pipe(map((data: any) => {
                return data.data.collection;
            }));
    }

    public create(collection: any): Observable<any> {

        return this.apollo.mutate({
            mutation: createCollectionMutation,
            variables: {
                input: collection,
            },
        }).pipe(map(({data: {createCollection}}: any) => createCollection));

    }

    public update(collection): Observable<any> {

        const ignoreFields = [
            'id',
            'creationDate',
            'updateDate',
            'creator',
            'updater',
            '__typename',
        ];
        const collectionInput = omit(collection, ignoreFields);

        return this.apollo.mutate({
            mutation: updateCollectionMutation,
            variables: {
                id: collection.id,
                input: collectionInput,
            },
        });
    }

    public delete(collection: any): Observable<any> {
        return this.apollo.mutate({
            mutation: deleteCollectionMutation,
            variables: {
                id: collection.id,
            },
        });
    }

}

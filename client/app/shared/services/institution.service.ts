import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Observable } from 'rxjs/Observable';
import {
    createInstitutionMutation, deleteInstitutionMutation, institutionQuery, institutionsQuery, updateInstitutionMutation,
} from '../queries/institution';
import 'rxjs/add/observable/of';
import { filter, map } from 'rxjs/operators';
import { merge, omit } from 'lodash';

@Injectable()
export class InstitutionService {

    constructor(private apollo: Apollo) {
    }

    public watchAll(variables): Observable<any> {

        const query = this
            .apollo
            .watchQuery({
                query: institutionsQuery,
                fetchPolicy: 'cache-and-network',
            });

        variables.subscribe(data => {
            query.setVariables(data);
        });

        return query
            .valueChanges
            .pipe(filter((data: any) => !!data.data && !data.loading), map((data: any) => {
                return data.data.institutions;
            }));
    }

    public getOne(id): Observable<any> {
        return this
            .apollo
            .query({
                query: institutionQuery,
                variables: {
                    id: id,
                },
            })
            .pipe(map((data: any) => {
                return data.data.institution;
            }));
    }

    public create(institution: any): Observable<any> {

        return this.apollo.mutate({
            mutation: createInstitutionMutation,
            variables: {
                input: institution,
            },
        }).pipe(map(({data: {createInstitution}}: any) => createInstitution));

    }

    public update(institution): Observable<any> {

        const ignoreFields = [
            'id',
            'artists',
            'creationDate',
            'updateDate',
            'creator',
            'updater',
            'height',
            'width',
            '__typename',
        ];
        const institutionInput = omit(institution, ignoreFields);

        return this.apollo.mutate({
            mutation: updateInstitutionMutation,
            variables: {
                id: institution.id,
                input: institutionInput,
            },
        });
    }

    public delete(institution: any): Observable<any> {
        return this.apollo.mutate({
            mutation: deleteInstitutionMutation,
            variables: {
                id: institution.id,
            },
        });
    }

}

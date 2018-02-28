import gql from 'graphql-tag';
import { Apollo } from 'apollo-angular';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs/Observable';
import { MutationsQuery } from '../generated-types';
import { FetchResult } from 'apollo-link';
import { UtilityService } from './utility.service';

@Injectable()
export class LinkMutationService {

    /**
     * Query to get list of mutations
     */
    private queriesQuery = gql`query Mutations{
      __type(name:"Mutation") {
        fields {
          name
        }
      }
    }`;

    /**
     * Receives the list of available mutation names
     */
    private mutationNames: MutationsQuery['__type']['fields'];

    /**
     * Mutation name pattern
     * @type {string}
     */
    private readonly mutationName = `#action#upperName1#upperName2`;

    /**
     * Mutation pattern
     * @type {string}
     */
    private readonly mutationModel = `mutation linkAndUnlink {
        #function(#name1: #id1, #name2: #id2) {
            id
        }
    }`;

    constructor(private apollo: Apollo) {
        this.apollo.query<MutationsQuery>({
            query: this.queriesQuery,
        }).subscribe(({data}) => {
            this.mutationNames = data.__type.fields;
        });
    }

    /**
     * Link two objects together
     * @param obj1
     * @param obj2
     * @returns {Observable<FetchResult<any>>}
     */
    public link(obj1, obj2): Observable<FetchResult<any>> {
        const mutation = this.getMutation('link', obj1, obj2);

        return this.execute(gql(mutation), obj1, obj2);
    }

    /**
     * Unlink two objects
     * @param obj1
     * @param obj2
     * @returns {Observable<FetchResult<any>>}
     */
    public unlink(obj1, obj2): Observable<FetchResult<any>> {
        const mutation = this.getMutation('unlink', obj1, obj2);

        return this.execute(gql(mutation), obj1, obj2);
    }

    /**
     * Generate mutation using patters and replacing variables
     * @param action
     * @param obj1
     * @param obj2
     * @param {boolean} tryReverse
     * @returns {string}
     */
    private getMutation(action: string, obj1, obj2, tryReverse = true): string {
        const name = this.replaceVars(this.mutationName, obj1, obj2).replace('#action', action);

        if (this.mutationNames.find(mut => mut.name === name)) {
            return this.replaceVars(this.mutationModel.replace('#function', name), obj1, obj2);
        }

        const result = tryReverse ? this.getMutation(action, obj2, obj1, false) : null;
        if (!result) {
            throw TypeError('API does not allow to ' + action + ' ' + obj1.__typename + ' and ' + obj2.__typename);

        }

        return result;
    }

    /**
     * Execute mutation
     * @param mutation
     * @param obj1
     * @param obj2
     * @returns {Observable<FetchResult<any>>}
     */
    private execute(mutation, obj1, obj2): Observable<FetchResult<any>> {

        const variables = {};
        variables[UtilityService.lowerCaseFirstLetter(obj1.__typename)] = obj1.id;
        variables[UtilityService.lowerCaseFirstLetter(obj2.__typename)] = obj2.id;

        return this.apollo.mutate({
            mutation: mutation,
            variables: variables,
        });
    }

    /**
     * Replace name and ids in mutation(Name) pattern.
     * @param string
     * @param obj1
     * @param obj2
     */
    private replaceVars(string: string, obj1, obj2): string {

        return string
            .replace('#name1', UtilityService.lowerCaseFirstLetter(obj1.__typename))
            .replace('#upperName1', obj1.__typename)
            .replace('#id1', obj1.id)
            .replace('#name2', UtilityService.lowerCaseFirstLetter(obj2.__typename))
            .replace('#upperName2', obj2.__typename)
            .replace('#id2', obj2.id);
    }

}

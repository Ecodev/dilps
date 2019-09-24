import { cloneDeep, defaultsDeep, isArray, mergeWith } from 'lodash';
import { BehaviorSubject } from 'rxjs';

import { CardFilter } from '../generated-types';
import { Literal } from '../types';

export interface QueryVariables {
    filter?: Filter | null;
    pagination?: PaginationInput | null;
    sorting?: Array<Sorting> | null;
}

type Filter = CardFilter;

export interface PaginationInput {
    offset?: number | null;
    pageIndex?: number | null;
    pageSize?: number | null;
}

export interface Sorting {
    field: string;
    order?: SortingOrder | null;
}

export enum SortingOrder {
    ASC = 'ASC',
    DESC = 'DESC',
}

/**
 * During lodash merge, overrides arrays
 */
function mergeOverrideArray(destValue, source) {
    if (isArray(source)) {
        return source;
    }
}

/**
 * During lodash merge, concat arrays
 */
function mergeConcatArray(destValue, source) {
    if (isArray(source)) {
        if (destValue) {
            return destValue.concat(source);
        } else {
            return source;
        }
    }
}

/**
 * Filter manager stores a set of channels that contain a variable object and exposes an observable "variables" that updates with the result
 * of all channels merged together.
 *
 * A channel is supposed to be used by an aspect of the GUI (pagination, sorting, search, others ?).
 * Each
 *
 * const fm = new FilterManager();
 * fm.merge('componentA-variables', {a : [1, 2, 3]});
 *
 * Variables attributes is a BehaviorSubject. That mean it's not mandatory to subscribe, we can just call getValue or value attributes on
 * it : console.log(fm.variables.value); //  {a : [1, 2, 3]}
 *
 * Set new variables for 'componentA-variables'
 * fm.merge('componentA-variables', {a : [1, 2]);
 * console.log(fm.variables.value); //  {a : [1, 2, 3]}
 *
 * Set new variables for new channel
 * fm.merge('componentB-variables', {a : [3, 4]);
 * console.log(fm.variables.value); // {a : [1, 2, 3, 4]}
 */
export class QueryVariablesManager<T extends QueryVariables = QueryVariables> {

    public readonly variables: BehaviorSubject<T> = new BehaviorSubject<T>(null);
    private readonly channels: Map<string, T> = new Map<string, T>();

    constructor() {
    }

    /**
     * Set or override all the variables that may exist in the given channel
     */
    public set(channelName: string, variables: T) {
        this.channels.set(channelName, cloneDeep(variables)); // cloneDeep to change reference and prevent some interactions when merge
        this.updateVariables();
    }

    public get(channelName: string) {
        // Avoid to return the same reference to prevent an attribut change, then another channel update that would used this changed
        // attribute without having explicitely asked QueryVariablesManager to update it.
        return cloneDeep(this.channels.get(channelName));
    }

    /**
     * Merge variable into a channel, overriding arrays in same channel / key
     */
    public merge(channelName: string, newVariables: T) {
        const variables = this.channels.get(channelName);
        if (variables) {
            mergeWith(variables, cloneDeep(newVariables), mergeOverrideArray); // merge preserves references, cloneDeep prevent that
            this.updateVariables();
        } else {
            this.set(channelName, newVariables);
        }
    }

    /**
     * Apply default values to a channel
     * Note : lodash defaults only defines values on destinations keys that are undefined
     */
    public defaults(channelName: string, newVariables: T) {
        const variables = this.channels.get(channelName);
        if (variables) {
            defaultsDeep(variables, newVariables);
            this.updateVariables();
        } else {
            this.set(channelName, newVariables);
        }
    }

    /**
     * Merge channels together
     * Arrays are concatenated
     */
    public updateVariables() {

        let naturalSearch = this.channels.get('natural-search');
        naturalSearch = naturalSearch && naturalSearch.filter && naturalSearch.filter.groups ? cloneDeep(naturalSearch) : null;
        const mergedVariables = naturalSearch ? naturalSearch : {};

        this.channels.forEach((variables: Literal | BehaviorSubject<Literal>, channelName: string) => {

            if (channelName === 'natural-search') {
                return;
            }

            if (variables instanceof BehaviorSubject) {
                variables = variables.getValue();
            }

            if (naturalSearch && variables && variables.filter && variables.filter.groups && variables.filter.groups.length === 1) {
                this.applyConditionsOnEachGroup(mergedVariables, variables.filter.groups[0].conditions);
            } else {
                mergeWith(mergedVariables, variables, mergeConcatArray);
            }

        });

        this.variables.next(mergedVariables as T);
    }

    /**
     * @param destVariables destination variables
     * @param srcConditions Conditions source that will by merged into each filter groups
     */
    private applyConditionsOnEachGroup(destVariables: any, srcConditions: Literal[]) {

        if (destVariables.filter && destVariables.filter.groups) {
            for (const group of destVariables.filter.groups) {
                mergeWith(group, {conditions: cloneDeep(srcConditions)}, mergeConcatArray);
            }
        }

        return destVariables;
    }

}

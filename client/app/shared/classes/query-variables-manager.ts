import { Literal } from '../types';
import { BehaviorSubject } from 'rxjs';
import { cloneDeep, defaultsDeep, isArray, mergeWith } from 'lodash';

/**
 * During lodash merge, overrides arrays
 * @param value
 * @param source
 * @returns {any[]}
 */
function mergeOverrideArray(destValue, source) {
    if (isArray(source)) {
        return source;
    }
}

/**
 * During lodash merge, concat arrays
 * @param value
 * @param source
 * @returns {any}
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
export class QueryVariablesManager<T = Literal> {

    private readonly channels: Map<string, T> = new Map<string, T>();
    private readonly contextChannels: Map<string, Literal> = new Map<string, Literal>();
    public readonly variables: BehaviorSubject<T> = new BehaviorSubject<T>(null);

    constructor() {
    }

    /**
     * Set or override all the variables that may exist in the given channel
     * @param {string} channelName
     * @param {Literal} variables
     */
    public set(channelName: string, variables: T) {

        const contextFields = this.contextChannels.get(channelName);
        if (contextFields) {
            variables = this.applyFieldsOnEachConditionField(variables, contextFields);
            this.channels.set(channelName, variables);
        }

        this.channels.set(channelName, cloneDeep(variables)); // cloneDeep to change reference and prevent some interactions when merge
        this.updateVariables();
    }

    /**
     * Merge variable into a channel, overriding arrays in same channel / key
     * @param {string} channelName key
     * @param {Literal} newVariables
     */
    public merge(channelName: string, newVariables: T) {
        let variables = this.channels.get(channelName);
        if (variables) {
            mergeWith(variables, cloneDeep(newVariables), mergeOverrideArray); // merge preserves references, cloneDeep prevent that

            const contextFields = this.contextChannels.get(channelName);
            if (contextFields) {
                variables = this.applyFieldsOnEachConditionField(variables, contextFields);
                this.channels.set(channelName, variables);
            }

            this.updateVariables();
        } else {
            this.set(channelName, newVariables);
        }
    }

    /**
     * Apply default values to a channel
     * Note : lodash defaults only defines values on destinations keys that are undefined
     * @param {string} channelName
     * @param {T} newVariables
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
        const mergedVariables = {};

        this.channels.forEach((variables: Literal | BehaviorSubject<Literal>) => {
            if (variables instanceof BehaviorSubject) {
                variables = variables.getValue();
            }
            mergeWith(mergedVariables, variables, mergeConcatArray);
        });

        this.variables.next(mergedVariables as T);
    }

    /**
     * Todo : replace second param "variables: T" by ConditionField[] (or whatever it's name is)
     * @param {string[]} channelName
     * @param {Literal} contextFields
     */
    public contextualizeChannel(channelName: string, contextFields: Literal) {

        // cloneDeep to change reference and prevent some interactions when merge
        this.contextChannels.set(channelName, cloneDeep(contextFields));

        let variables = this.channels.get(channelName) as any;
        if (variables) {
            variables = this.applyFieldsOnEachConditionField(variables, contextFields);
            this.channels.set(channelName, variables);
            this.updateVariables();
        }
    }

    /**
     *
     * @param {any[]} destConditions destination list of fields
     * @param srcFields Fields source that will by merged with conditions fields
     */
    private applyFieldsOnEachConditionField(destVariables: any, srcFields: Literal) {

        for (const condition of destVariables.filter.conditions) {
            mergeWith(condition, {fields: cloneDeep(srcFields)}, mergeConcatArray);
        }

        return destVariables;
    }

}

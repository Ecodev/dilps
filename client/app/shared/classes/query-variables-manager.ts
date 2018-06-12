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
    public readonly variables: BehaviorSubject<Literal> = new BehaviorSubject<Literal>(null);

    constructor() {
    }

    /**
     * Set or override all the variables that may exist in the given channel
     * @param {string} channelName
     * @param {Literal} variables
     */
    public set(channelName: string, variables: T) {
        this.channels.set(channelName, cloneDeep(variables)); // map preserves references, cloneDeep prevent that
        this.updateVariables();
    }

    /**
     * Merge variable into a channel, overriding arrays in same channel / key
     * @param {string} channelName key
     * @param {Literal} variables
     */
    public merge(channelName: string, variables: T) {
        const channel = this.channels.get(channelName);
        if (channel) {
            mergeWith(channel, cloneDeep(variables), mergeOverrideArray); // map preserves references, cloneDeep prevent that
            this.updateVariables();
        } else {
            this.set(channelName, variables);
        }
    }

    /**
     * Apply default values to a channel
     * Note : lodash defaults only defines values on destinations keys that are undefined
     * @param {string} channelName
     * @param {T} variables
     */
    public defaults(channelName: string, variables: T) {
        const channel = this.channels.get(channelName);
        if (channel) {
            defaultsDeep(channel, variables);
            this.updateVariables();
        } else {
            this.set(channelName, variables);
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

        this.variables.next(mergedVariables);
    }

}

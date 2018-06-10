import { Literal } from '../types';
import { BehaviorSubject } from 'rxjs';
import { cloneDeep, defaultsDeep, isArray, mergeWith } from 'lodash';

/**
 * Filter manager stores a set of channels that contain a filter object and exposes an observable "filters" that updates with the result
 * of all channels merged together.
 *
 * A channel is supposed to be used by an aspect of the GUI (pagination, sorting, search, others ?).
 * Each
 *
 * const fm = new FilterManager();
 * fm.merge('componentA-filters', {a : [1, 2, 3]});
 *
 * Filters attributes is a BehaviorSubject. That mean it's not mandatory to subscribe, we can just call getValue or value attributes on it :
 * console.log(fm.filters.value); //  {a : [1, 2, 3]}
 *
 * Set new filters for 'componentA-filters'
 * fm.merge('componentA-filters', {a : [1, 2]);
 * console.log(fm.filters.value); //  {a : [1, 2, 3]}
 *
 * Set new filters for new channel
 * fm.merge('componentB-filters', {a : [3, 4]);
 * console.log(fm.filters.value); // {a : [1, 2, 3, 4]}
 */
export class FilterManager<T = Literal> {

    private readonly channels: Map<string, T> = new Map<string, T>();
    public readonly filters: BehaviorSubject<Literal> = new BehaviorSubject<Literal>(null);

    /**
     * During lodash merge, overrides arrays
     * @param value
     * @param source
     * @returns {any[]}
     */
    static mergeOverrideArray(destValue, source) {
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
    static mergeConcatArray(destValue, source) {
        if (isArray(source)) {
            if (destValue) {
                return destValue.concat(source);
            } else {
                return source;
            }
        }
    }

    constructor() {
    }

    /**
     * Set or override all the filters that may exist in the given channel
     * @param {string} channelName
     * @param {Literal} filters
     */
    public set(channelName: string, filters: T) {
        this.channels.set(channelName, cloneDeep(filters)); // map preserves references, cloneDeep prevent that
        this.updateFilters();
    }

    /**
     * Merge filter into a channel, overriding arrays in same channel / key
     * @param {string} channelName key
     * @param {Literal} filters
     */
    public merge(channelName: string, filters: T) {
        const channel = this.channels.get(channelName);
        if (channel) {
            mergeWith(channel, cloneDeep(filters), FilterManager.mergeOverrideArray); // map preserves references, cloneDeep prevent that
            this.updateFilters();
        } else {
            this.set(channelName, filters);
        }
    }

    /**
     * Apply default values to a channel
     * Note : lodash defaults only defines values on destinations keys that are undefined
     * @param {string} channelName
     * @param {T} filters
     */
    public defaults(channelName: string, filters: T) {
        const channel = this.channels.get(channelName);
        if (channel) {
            defaultsDeep(channel, filters);
            this.updateFilters();
        } else {
            this.set(channelName, filters);
        }
    }

    /**
     * Merge channels together
     * Arrays are concatenated
     */
    public updateFilters() {
        const mergedFilters = {};

        this.channels.forEach((filters: Literal | BehaviorSubject<Literal>) => {
            if (filters instanceof BehaviorSubject) {
                filters = filters.getValue();
            }
            mergeWith(mergedFilters, filters, FilterManager.mergeConcatArray);
        });

        this.filters.next(mergedFilters);
    }

}

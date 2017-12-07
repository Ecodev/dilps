import { Observable } from 'rxjs/Observable';
import { merge } from 'lodash';
import { Injectable } from '@angular/core';
import { map } from 'rxjs/operators';

@Injectable()
export class QueryVariablesService {

    /**
     *
     * @param variables
     * @param completeWith
     * @param defaults
     * @returns {any}
     */
    public static getVariables(variables, defaults = {}, completeWith = {}) {
        if (variables instanceof Observable) {
            const vars: Observable<any> = variables.pipe(map(v => this.defaultsVariables(v, defaults, completeWith)));
            return this.observableToPlain(vars, defaults);
        } else {
            return this.defaultsVariables(variables, defaults, completeWith);
        }
    }

    private static defaultsVariables(variables, defaults, completeWith) {
        return merge({}, defaults, completeWith, variables);
    }

    /**
     * Transform an observable that contains attributes into a plain object where each attribute contains it's own observable
     * @param {Observable<any>} variables
     * @param defaults
     * @returns {{}}
     */
    private static observableToPlain(variables: Observable<any>, defaults): Object {
        const result = {};
        Object.keys(defaults).forEach(key => {
            result[key] = variables.pipe(map(v => v[key]));
        });
        return result;
    }

}

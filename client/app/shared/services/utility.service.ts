import { isEmpty, isObject, pickBy } from 'lodash';
import { Literal } from '../types';

export class UtilityService {

    public static relationsToIds(object: Literal): Literal {
        const newObj = {};
        Object.keys(object).forEach((key) => {
            let value = object[key];
            if (isObject(value) && value.id) {
                value = object[key].id;
            } else if (isObject(value) && !(value instanceof File)) {
                value = pickBy(value, (v, k) => k !== '__typename');
            }

            newObj[key] = value;
        });
        return newObj;
    }

    /**
     * Remove from source object the attributes with same value as modified
     * Does not consider arrays
     */
    public static cleanSameValues(source: Literal, modified: Literal): Literal {
        Object.keys(source).forEach(key => {
            if (source[key] instanceof Object) {
                this.cleanSameValues(source[key], modified[key]);
                if (isEmpty(source[key])) {
                    delete source[key];
                }
            } else if (modified && source[key] === modified[key]) {
                delete source[key];
            }
        });

        return source;
    }

    /**
     * Returns the plural form of the given name
     * @param {string} name
     * @returns {string}
     */
    public static makePlural(name: string): string {
        const plural = name + 's';

        return plural.replace(/ys$/, 'ies').replace(/ss$/, 'ses');
    }

    /**
     * Returns the string with the first letter as capital
     * @param {string} string
     * @returns {string}
     */
    public static upperCaseFirstLetter(string: string): string {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * Returns the string with the first letter as lower case
     */
    public static lowerCaseFirstLetter(string: string): string {
        return string.charAt(0).toLowerCase() + string.slice(1);
    }
}

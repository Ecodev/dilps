import { isEmpty } from 'lodash';

export class UtilityService {

    public static relationsToIds(object: Object) {
        const newObj = {};
        Object.keys(object).forEach((name) => {
            newObj[name] = object[name] && object[name].id ? object[name].id : object[name];
        });
        return newObj;
    }

    /**
     * Remove from source object the attributes with same value as modified
     * Does not consider arrays
     * @param source
     * @param modified
     */
    public static cleanSameValues(source, modified) {
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

}

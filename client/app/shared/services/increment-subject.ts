import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { merge } from 'lodash';

/**
 * Same as BehaviorSubject with same functions
 * Expose additional function patch() that prevents to override old value, bug merges new one in old one (for object variables usage)
 */
export class IncrementSubject extends BehaviorSubject<any> {

    constructor(_val: any) {
        super(_val);
    }

    public patch(value: Object) {
        this.next(merge({}, this.value, value));
    }
}

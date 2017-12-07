import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { NgProgress } from 'ngx-progressbar';

@Injectable()
export class NetworkActivityService {

    /**
     * Count pending requests
     * @type {number}
     */
    public pending = 0;

    /**
     * Observable specifying if app is loading or not
     * @type {BehaviorSubject<number>}
     */
    public readonly isPending = new BehaviorSubject<boolean>(false);
    public readonly errors = new BehaviorSubject<any[]>([]);

    constructor(private progressService: NgProgress) {
    }

    public increase() {

        if (this.pending === 0) {
            this.progressService.start();
        }

        this.pending++;
        this.isPending.next(this.pending > 0);
    }

    public decrease() {
        this.pending--;
        this.isPending.next(this.pending > 0);

        // Mark progress a completed, after waiting 20ms in case a refetchQueries would be used
        if (this.pending === 0) {
            setTimeout(() => {
                if (this.pending === 0) {
                    this.progressService.done();
                }
            }, 20);

        }
    }

    public updateErrors(response) {
        if (response && response.errors && response.errors.length) {
            this.errors.next([].concat.apply([], response.errors));
        }
    }

}

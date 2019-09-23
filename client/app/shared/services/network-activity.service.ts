import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { NgProgress } from '@ngx-progressbar/core';

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
            this.progressService.ref().start();
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
                    this.progressService.ref().complete();
                }
            }, 20);

        }
    }

    public updateErrors(errors) {
        if (errors && errors.length) {
            this.errors.next(errors);
        }
    }

}

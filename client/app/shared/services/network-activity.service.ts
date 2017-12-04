import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { Apollo } from 'apollo-angular';
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

    constructor(private apollo: Apollo, private progressService: NgProgress) {
        const self = this;


        // // New request : increment
        // this.apollo.getClient().networkInterface.use([
        //     {
        //         applyBatchMiddleware(req, next) {
        //             self.increase();
        //             next();
        //         },
        //     },
        // ]);
        //
        // // Receive response : decrement
        // this.apollo.getClient().networkInterface.useAfter([
        //     {
        //         applyBatchAfterware({responses}, next) {
        //             self.updateErrors(responses);
        //             self.decrease();
        //             next();
        //         },
        //     },
        // ]);
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

        // Mark progress a completed, after waiting 20ms in case a refrechQueries would be used
        if (this.pending === 0) {
            setTimeout(() => {
                if (this.pending === 0) {
                    this.progressService.done();
                }
            }, 20);

        }
    }

    private updateErrors(responses) {
        if (responses.length) {
            const errors = responses.map(r => r.errors).filter(er => !!er);
            this.errors.next([].concat.apply([], errors));
        }
    }

}

import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { UserService } from './user.service';

@Injectable()
export class UserResolver implements Resolve<any> {

    constructor(private userSvc: UserService) {
    }

    /**
     * Resolve sites for routing service only at the moment
     * @param {ActivatedRouteSnapshot} route
     * @returns {any}
     */
    public resolve(route: ActivatedRouteSnapshot): Observable<any> {

        if (route.params['userId']) {
            return this.userSvc.getOne(route.params['userId']);
        }

        return this.userSvc.getCurrentUser();
    }

}

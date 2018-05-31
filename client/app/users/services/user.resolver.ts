import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve } from '@angular/router';
import { Observable } from 'rxjs';
import { UserService } from './user.service';
import { ViewerQuery } from '../../shared/generated-types';

@Injectable()
export class UserResolver implements Resolve<any> {

    constructor(private userSvc: UserService) {
    }

    /**
     * Resolve sites for routing service only at the moment
     * @param {ActivatedRouteSnapshot} route
     * @returns {any}
     */
    public resolve(route: ActivatedRouteSnapshot): Observable<ViewerQuery['viewer']> {
        return this.userSvc.getCurrentUser();
    }

}

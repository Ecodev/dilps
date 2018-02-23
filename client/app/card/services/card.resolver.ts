import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { CardService } from './card.service';

@Injectable()
export class CardResolver implements Resolve<any> {

    constructor(private cardSvc: CardService) {
    }

    /**
     * Resolve sites for routing service only at the moment
     * @param {ActivatedRouteSnapshot} route
     * @returns {any}
     */
    public resolve(route: ActivatedRouteSnapshot): Observable<any> {

        if (route.params['cardId']) {
            return this.cardSvc.getOne(route.params['cardId']);
        }

    }

}

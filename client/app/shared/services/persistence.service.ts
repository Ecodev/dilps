import { Injectable } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { clone, merge } from 'lodash';

@Injectable({
    providedIn: 'root',
})
export class PersistenceService {

    constructor(private router: Router) {
    }

    public persist(key: string, value: any, route: ActivatedRoute) {
        this.persistInUrl(key, value, route);
        this.persistInStorage(key, value);
    }

    public persistInUrl(key: string, value: any, route: ActivatedRoute) {
        console.warn('persistInUrl', key, value);
        console.log('is root route', !route.snapshot.url.length, route);

        const params = clone(route.snapshot.queryParams);
        console.log('params', params);

        params[key] = JSON.stringify(value);
        this.router.navigate([], {queryParams: params});
    }

    public persistInStorage(key: string, value: any) {

    }

    public get(key: string, route: ActivatedRoute): any {
        const params = this.getFromUrl(key, route);

        // Warning : default merge used for now
        // Take care if two different keys return arrays that will conflict.
        // Dunno what should be done, probably override but which prioriy ? url > storage or storage > url
        return merge(params, this.getFromStorage(key));
    }

    public getFromUrl(key: string, route: ActivatedRoute): any {
        return JSON.parse(route.snapshot.queryParamMap.get(key));
    }

    public getFromStorage(key: string): any {

    }

}

import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { ImageService } from './image.service';

@Injectable()
export class ImageResolver implements Resolve<any> {

    constructor(private imageSvc: ImageService) {
    }

    /**
     * Resolve sites for routing service only at the moment
     * @param {ActivatedRouteSnapshot} route
     * @returns {any}
     */
    public resolve(route: ActivatedRouteSnapshot): Observable<any> {

        if (route.params['imageId']) {
            return this.imageSvc.getOne(route.params['imageId']);
        }

    }

}

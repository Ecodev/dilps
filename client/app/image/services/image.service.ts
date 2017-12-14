import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Observable } from 'rxjs/Observable';
import { createImageMutation, deleteImageMutation, imageQuery, imagesQuery, updateImageMutation } from '../../shared/queries/image';
import 'rxjs/add/observable/of';
import { filter, map } from 'rxjs/operators';
import { omit } from 'lodash';

@Injectable()
export class ImageService {

    constructor(private apollo: Apollo) {
    }

    public watchAll(): Observable<any> {

        const query = this
            .apollo
            .watchQuery({
                query: imagesQuery,
                variables: {
                    pageIndex: 0,
                    pageSize: 1,
                },
                fetchPolicy: 'cache-and-network',
            });

        return query
            .valueChanges
            .pipe(filter((data: any) => !!data.data), map((data: any) => {
                return data.data.images;
            }));
    }

    public getOne(id): Observable<any> {
        return this
            .apollo
            .query({
                query: imageQuery,
                variables: {
                    id: id,
                },
            })
            .pipe(map((data: any) => {
                return data.data.image;
            }));
    }

    public create(image: any): Observable<any> {

        return this.apollo.mutate({
            mutation: createImageMutation,
            variables: {
                input: image,
            },
        }).pipe(map(({data: {createImage}}: any) => createImage));

    }

    public update(image): Observable<any> {

        const ignoreFields = [
            'id',
            'artists',
            'creationDate',
            'updateDate',
            'creator',
            'updater',
            '__typename',
        ];
        const imageInput = omit(image, ignoreFields);

        return this.apollo.mutate({
            mutation: updateImageMutation,
            variables: {
                id: image.id,
                input: imageInput,
            },
        });
    }

    public delete(image: any): Observable<any> {
        return this.apollo.mutate({
            mutation: deleteImageMutation,
            variables: {
                id: image.id,
            },
        });
    }
}

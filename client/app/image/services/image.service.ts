import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import 'rxjs/add/observable/of';
import { cloneDeep, merge, omit } from 'lodash';
import { CreateImageMutation, DeleteImageMutation, ImageQuery, ImagesQuery, UpdateImageMutation, } from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { createImageMutation, deleteImageMutation, imageQuery, imagesQuery, updateImageMutation } from './imageQueries';

@Injectable()
export class ImageService extends AbstractModelService<ImageQuery['image'],
    ImagesQuery['images'],
    CreateImageMutation['createImage'],
    UpdateImageMutation['updateImage'],
    DeleteImageMutation['deleteImage']> {

    public static getImageFormat(image, height): any {
        height = Math.min(image.height, height);
        const ratio = image.width / image.height;
        return {
            height: height,
            width: height * ratio,
        };
    }

    public static formatImage(image, height) {
        const sizes = this.getImageFormat(image, height);
        const imageLink = '/image-src/' + image.id + '/';
        const fields = {src: imageLink + sizes.height};
        return merge({}, image, fields);
    }

    constructor(apollo: Apollo) {
        super(apollo, 'image', imageQuery, imagesQuery, createImageMutation, updateImageMutation, deleteImageMutation);
    }
}

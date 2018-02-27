import { Injectable } from '@angular/core';
import { Subject } from 'rxjs/Subject';

@Injectable()
export class UploadService {

    /**
     * Stores pending image to keep information after router navigates to /card/new
     * @type {null}
     */
    public static pending = null;

    public readonly filesChanged = new Subject<File[]>();

    constructor() {

    }

}

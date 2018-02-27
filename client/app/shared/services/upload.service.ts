import { Injectable } from '@angular/core';
import { Subject } from 'rxjs/Subject';

@Injectable()
export class UploadService {

    public readonly filesChanged = new Subject<File[]>();

    public pending = null;

    constructor() {

    }

}

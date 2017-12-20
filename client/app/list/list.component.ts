import { Component, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ImageService } from '../image/services/image.service';
import { merge } from 'lodash';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';

@Component({
    selector: 'app-list',
    templateUrl: './list.component.html',
    styleUrls: ['./list.component.scss'],
})
export class ListComponent implements OnInit {

    @ViewChild('gallery') gallery;

    public images = null;
    public options = null;
    public selected;

    public showLogo = true;

    private thumbnailHeight = 400;
    private enlargedHeight = 2000;
    private sub;
    private filter;

    constructor(private router: Router, private route: ActivatedRoute, private imageSvc: ImageService) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => this.showLogo = data.showLogo);

        this.options = {
            margin: 5,
            showLabels: 'true',
            rowHeight: this.thumbnailHeight,
        };

    }

    private formatImages(images) {

        images = images.map(image => {
            let thumb = ImageService.formatImage(image, this.thumbnailHeight);
            let big = ImageService.formatImage(image, this.enlargedHeight);

            thumb = {
                thumbnail: thumb.src,
                tWidth: thumb.width,
                tHeight: thumb.height,
            };

            big = {
                enlarged: big.src,
                eWidth: big.width,
                eHeight: big.height,
            };

            const fields = {
                link: 'true',
                title: image.name,
            };

            return merge({}, image, thumb, big, fields);
        });

        return images;
    }

    public activate(item) {
        this.router.navigate([
            'image',
            item.id,
        ]);
    }

    public select(items) {
        this.selected = items;

    }

    public loadMore(ev) {

        if (!this.sub) {

            this.filter = new BehaviorSubject<any>({
                pagination: {
                    offset: ev.offset,
                    pageSize: ev.limit,
                },
            });
            this.sub = this.imageSvc.watchAll(this.filter);
            this.sub.subscribe(data => {
                this.gallery.addItems(this.formatImages(data.items));
            });

        } else {
            this.filter.next({
                pagination: {
                    offset: ev.offset,
                    pageSize: ev.limit,
                },
            });
        }

    }

}

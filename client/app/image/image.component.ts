import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { ImageService } from './services/image.service';
import { AlertService } from '../shared/services/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
import { merge } from 'lodash';

@Component({
    selector: 'app-image',
    templateUrl: './image.component.html',
    styleUrls: ['./image.component.scss'],
})
export class ImageComponent implements OnInit {

    public data: any = {
        name: '',
    };

    public edit = false;

    constructor(private route: ActivatedRoute,
        private router: Router,
        public themeSvc: ThemeService,
        private imageSvc: ImageService,
        private alertSvc: AlertService) {
    }

    ngOnInit() {
        const image = this.route.snapshot.data['image'];
        if (image) {
            merge(this.data, image);
        }
    }

    public onSubmit() {
        if (this.data.id) {
            this.update();
        } else {
            this.create();
        }
    }

    public update() {
        this.imageSvc.update(this.data).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.imageSvc.create(this.data).subscribe(image => {
            this.alertSvc.info('Créé');
            this.router.navigate([
                '..',
                image.id,
            ], {relativeTo: this.route});
        });
    }

    public confirmDelete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.imageSvc.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.router.navigateByUrl('/');
                    });
                }
            });
    }

}

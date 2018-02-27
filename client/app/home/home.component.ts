import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { UserService } from '../users/services/user.service';
import { NetworkActivityService } from '../shared/services/network-activity.service';
import { MatDialog, MatSnackBar } from '@angular/material';
import { AlertService } from '../shared/components/alert/alert.service';
import { UserComponent } from '../users/user/user.component';
import { UploadService } from '../shared/services/upload.service';
import { ActivatedRoute, Router } from '@angular/router';
import { isArray } from 'lodash';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
})
export class HomeComponent implements OnInit {

    public initialized;

    public errors = [];

    constructor(public themeSvc: ThemeService,
                public route: ActivatedRoute,
                public router: Router,
                public userSvc: UserService,
                private network: NetworkActivityService,
                private snackBar: MatSnackBar,
                private alertSvc: AlertService,
                private dialog: MatDialog,
                public uploadSvc: UploadService) {
    }

    ngOnInit() {

        // Watch errors
        this.network.errors.subscribe(errors => {
            this.errors = this.errors.concat(errors);
            if (errors.length) {
                this.alertSvc.error('Quelque chose s\'est mal passé !');
            }
        });

        this.uploadSvc.filesChanged.subscribe(files => {
            this.addPhotos(files);
        });
    }

    public addPhotos(files) {

        try {
            const routeUrlTree = this.router.createUrlTree([this.route]);
            const routeIsActive = this.router.isActive(routeUrlTree, true);

            if (!routeIsActive) {
                this.uploadPhoto(files);
            }

        } catch (e) {
            this.uploadPhoto(files);
        }
    }

    public uploadPhoto(files) {
        this.uploadSvc.pending = isArray(files) ? files[files.length - 1] : files;
        this.router.navigateByUrl('/card/new');
    }

    public editUser() {
        this.userSvc.getCurrentUser().subscribe(user => {
            this.dialog.open(UserComponent, {
                width: '800px',
                data: {item: user},
            });
        });
    }

}

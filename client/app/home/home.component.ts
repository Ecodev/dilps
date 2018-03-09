import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { UserService } from '../users/services/user.service';
import { NetworkActivityService } from '../shared/services/network-activity.service';
import { MatDialog, MatSnackBar } from '@angular/material';
import { AlertService } from '../shared/components/alert/alert.service';
import { UserComponent } from '../users/user/user.component';
import { UploadService } from '../shared/services/upload.service';
import { ActivatedRoute, Router } from '@angular/router';
import { CardService } from '../card/services/card.service';
import { Observable } from 'rxjs/Observable';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
})
export class HomeComponent implements OnInit {

    public initialized;
    public errors = [];
    public user;

    constructor(public themeSvc: ThemeService,
                public route: ActivatedRoute,
                public router: Router,
                public userSvc: UserService,
                private network: NetworkActivityService,
                private snackBar: MatSnackBar,
                private alertSvc: AlertService,
                private dialog: MatDialog,
                public uploadSvc: UploadService,
                private cardSvc: CardService) {
    }

    ngOnInit() {
        // Watch errors
        this.network.errors.subscribe(errors => {
            this.errors = this.errors.concat(errors);
            if (errors.length) {
                this.alertSvc.error('Quelque chose s\'est mal passé !');
            }
        });

        this.userSvc.getCurrentUser().subscribe(user => {
            this.user = user;
        });
    }

    public uploadPhoto(files) {
        const observables = [];
        for (const file of files) {
            const card = this.cardSvc.getEmptyObject();
            card.file = file;
            observables.push(this.cardSvc.create(card));
        }
        Observable.forkJoin(observables).subscribe(() => {
            this.router.navigateByUrl('my-collection');
        });
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

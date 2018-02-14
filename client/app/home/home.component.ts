import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { UserService } from '../users/services/user.service';
import { NetworkActivityService } from '../shared/services/network-activity.service';
import { MatDialog, MatSnackBar } from '@angular/material';
import { AlertService } from '../shared/components/alert/alert.service';
import { UserComponent } from '../users/user/user.component';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
})
export class HomeComponent implements OnInit {

    public initialized;

    public errors = [];

    constructor(public themeSvc: ThemeService,
                public userSvc: UserService,
                private network: NetworkActivityService,
                private snackBar: MatSnackBar,
                private alertSvc: AlertService,
                private dialog: MatDialog) {
    }

    ngOnInit() {

        // Watch errors
        this.network.errors.subscribe(errors => {
            this.errors = this.errors.concat(errors);
            if (errors.length) {
                this.alertSvc.error('Quelque chose s\'est mal passé !');
            }
        });
    }

    public editUser() {
        this.userSvc.getCurrentUser().subscribe(user => {
            this.dialog.open(UserComponent, {
                width: '800px',
                data: {user: user},
            });
        });
    }

}

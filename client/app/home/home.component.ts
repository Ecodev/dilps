import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { UserService } from '../users/services/user.service';
import { NetworkActivityService } from '../shared/services/network-activity.service';
import { MatSnackBar } from '@angular/material';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
})
export class HomeComponent implements OnInit {

    public initialized;

    public errors = [];

    constructor(private themeSvc: ThemeService,
        private userSvc: UserService,
        private network: NetworkActivityService,
        private snackBar: MatSnackBar) {
    }

    ngOnInit() {

        // Watch errors
        this.network.errors.subscribe(errors => {
            console.log('errors', errors);
            this.errors = this.errors.concat(errors);
            if (errors.length) {
                this.snackBar.open('Quelque chose s\'est mal passé !', null, {
                    duration: 1500,
                    extraClasses: ['snackbar-error'],
                    verticalPosition: 'bottom',
                    horizontalPosition: 'start',
                });
            }
        });
    }

}

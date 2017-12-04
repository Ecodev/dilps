import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ThemeService } from '../../shared/services/theme.service';
import { UserService } from '../services/user.service';
import { merge } from 'lodash';

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
    styleUrls: ['./user.component.scss'],
})
export class UserComponent implements OnInit {

    public data: any = {};

    public theme: string;

    constructor(private route: ActivatedRoute, public themeSvc: ThemeService, private userSvc: UserService) {
    }

    ngOnInit() {
        const user = this.route.snapshot.data['user'];
        if (user) {
            merge(this.data, user);
        }

        this.themeSvc.theme.subscribe(theme => {
            this.theme = theme;
        });
    }

    public setTheme(theme) {
        this.themeSvc.set(theme);
    }

    public logout() {
        this.userSvc.logout();
    }

    public onSubmit() {
        this.userSvc.update(this.data);
    }
}

import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ThemeService } from '../../shared/services/theme.service';
import { UserService } from '../services/user.service';
import { merge } from 'lodash';
import { AlertService } from '../../shared/services/alert.service';

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
    styleUrls: ['./user.component.scss'],
})
export class UserComponent implements OnInit {

    public data: any = {};

    public theme: string;

    constructor(private route: ActivatedRoute,
        private router: Router,
        public themeSvc: ThemeService,
        private userSvc: UserService,
        private alertSvc: AlertService) {
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

    public save() {
        this.userSvc.update(this.data).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public confirmDelete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.userSvc.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.router.navigate(['..'], {relativeTo: this.route});
                    });
                }
            });
    }

}

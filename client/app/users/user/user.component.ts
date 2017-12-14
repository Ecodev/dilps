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

    public data: any = {
        type: 'default',
    };

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

    }

    public onSubmit() {
        if (this.data.id) {
            this.update();
        } else {
            this.create();
        }
    }

    public update() {
        this.userSvc.update(this.data).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.userSvc.create(this.data).subscribe(user => {
            this.alertSvc.info('Créé');
            this.router.navigate([
                '..',
                user.id,
            ], {relativeTo: this.route});
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

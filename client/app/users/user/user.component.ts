import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ThemeService } from '../../shared/services/theme.service';
import { UserService } from '../services/user.service';
import { merge } from 'lodash';
import { InstitutionService } from '../../institutions/services/institution.service';
import { AlertService } from '../../shared/components/alert/alert.service';

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
    styleUrls: ['./user.component.scss'],
})
export class UserComponent implements OnInit {

    public data: any = {
        type: 'default',
        institution: null
    };

    public theme: string;

    constructor(private route: ActivatedRoute,
        private router: Router,
        public themeSvc: ThemeService,
        public institutionSvc: InstitutionService,
        private userSvc: UserService,
        private alertSvc: AlertService) {
    }

    ngOnInit() {

        const user = this.route.snapshot.data['user'];
        if (user) {
            merge(this.data, user);
            if (!this.data.institution) {
                // this.data.institution = {};
            }
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

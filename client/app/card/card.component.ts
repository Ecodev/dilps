import { Component, Input, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { CardService } from './services/card.service';
import { ActivatedRoute, Router } from '@angular/router';
import { findKey, merge } from 'lodash';
import { InstitutionService } from '../institutions/services/institution.service';
import { AlertService } from '../shared/components/alert/alert.service';
import { ArtistService } from '../artists/services/artist.service';
import { ArtistComponent } from '../artists/artist/artist.component';
import { InstitutionComponent } from '../institutions/institution/institution.component';

@Component({
    selector: 'app-card',
    templateUrl: './card.component.html',
    styleUrls: ['./card.component.scss'],
})
export class CardComponent implements OnInit {

    @Input() id;

    public data: any = {
        name: '',
    };

    public edit = false;
    public status = 1;
    public imageSrc;
    public showLogo = false;

    public statuses = {
        1: {
            value: 'new',
            text: 'par moi',
        },
        2: {
            value: 'edited',
            text: 'par les membres',
            color: 'accent',
        },
        3: {
            value: 'reviewed',
            text: 'par tout le monde',
            color: 'primary',
        },
    };

    public institutionComponent = InstitutionComponent;
    public artistComponent = ArtistComponent;

    constructor(private route: ActivatedRoute,
                private router: Router,
                public themeSvc: ThemeService,
                private cardSvc: CardService,
                private alertSvc: AlertService,
                public artistService: ArtistService,
                public institutionSvc: InstitutionService) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => this.showLogo = data.showLogo);

        const card = this.route.snapshot.data['card'];
        if (card) {
            this.initCard(card);
        } else if (this.id) {
            this.cardSvc.getOne(this.id).subscribe(c => {
                this.initCard(c);
            });
        }
    }

    public initCard(card) {
        merge(this.data, card);

        // Init status
        this.status = +findKey(this.statuses, (s) => {
            return s.value === this.data.status;
        });

        this.imageSrc = CardService.formatImage(card, 2000).src;
    }

    public updateStatus(ev) {
        this.data.status = this.statuses[ev.value].value;
    }

    public onSubmit() {
        if (this.data.id) {
            this.update();
        } else {
            this.create();
        }
    }

    public update() {
        this.cardSvc.update(this.data).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.cardSvc.create(this.data).subscribe(card => {
            this.alertSvc.info('Créé');
            this.router.navigate([
                '..',
                card.id,
            ], {relativeTo: this.route});
        });
    }

    public confirmDelete() {
        this.alertSvc.confirm('Suppression', 'Voulez-vous supprimer définitivement cet élément ?', 'Supprimer définitivement')
            .subscribe(confirmed => {
                if (confirmed) {
                    this.cardSvc.delete(this.data).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.router.navigateByUrl('/');
                    });
                }
            });
    }

}

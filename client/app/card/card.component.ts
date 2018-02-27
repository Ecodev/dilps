import { Component, Input, OnChanges, OnDestroy, OnInit, SimpleChanges } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { CardService } from './services/card.service';
import { ActivatedRoute, Router } from '@angular/router';
import { findKey, merge } from 'lodash';
import { InstitutionService } from '../institutions/services/institution.service';
import { AlertService } from '../shared/components/alert/alert.service';
import { ArtistService } from '../artists/services/artist.service';
import { ArtistComponent } from '../artists/artist/artist.component';
import { InstitutionComponent } from '../institutions/institution/institution.component';
import { ChangeService } from '../changes/services/change.service';
import { UploadService } from '../shared/services/upload.service';

@Component({
    selector: 'app-card',
    templateUrl: './card.component.html',
    styleUrls: ['./card.component.scss'],
})
export class CardComponent implements OnInit, OnChanges, OnDestroy {

    @Input() public model;
    @Input() public title: string;
    @Input() public showLogo = false;

    @Input() public imageSrc;
    @Input() public imageData;

    public edit = false;
    public status = 1;

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
    private uploadSub;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private changeSvc: ChangeService,
                public themeSvc: ThemeService,
                private cardSvc: CardService,
                private alertSvc: AlertService,
                public artistService: ArtistService,
                public institutionSvc: InstitutionService,
                private uploadSvc: UploadService) {
    }

    ngOnInit() {

        this.route.data.subscribe(data => this.showLogo = data.showLogo);
        if (this.route.snapshot.data['card']) {
            this.model = merge({}, this.route.snapshot.data['card']); // merge to have editable object instead of apollo readonly object
            this.initCard();
        } else if (!this.model) {
            this.model = this.cardSvc.getEmptyObject();
            this.initCard();
            this.model.file = this.uploadSvc.pending;
            this.getBase64(this.uploadSvc.pending);
            this.edit = true;
        }

        this.uploadSub = this.uploadSvc.filesChanged.subscribe(files => {
            const file = files[files.length - 1];
            this.model.file = file;
            this.getBase64(file);
        });
    }

    ngOnChanges(changes: SimpleChanges) {
        this.initCard();
    }

    ngOnDestroy() {
        if (this.uploadSub) {
            this.uploadSub.unsubscribe();
        }
    }

    private getBase64(file) {

        if (!file) {
            return;
        }

        const reader = new FileReader();
        reader.addEventListener('load', (ev: any) => {
            this.imageData = btoa(ev.target.result);
        });
        reader.readAsBinaryString(file);
    }

    public initCard() {
        if (this.model) {

            // Init status
            this.status = +findKey(this.statuses, (s) => {
                return s.value === this.model.status;
            });

            const src = CardService.getImageLink(this.model, 2000);
            if (src) {
                this.imageSrc = src;
            }
        }
    }

    public updateStatus(ev) {
        this.model.status = this.statuses[ev.value].value;
    }

    public onSubmit() {
        if (this.model.id) {
            this.update();
        } else {
            this.create();
        }
    }

    public update() {
        this.cardSvc.update(this.model).subscribe(() => {
            this.alertSvc.info('Mis à jour');
        });
    }

    public create() {
        this.cardSvc.create(this.model).subscribe(card => {
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
                    this.cardSvc.delete(this.model).subscribe(() => {
                        this.alertSvc.info('Supprimé');
                        this.router.navigateByUrl('/');
                    });
                }
            });
    }

    public suggestUpdate() {
        this.router.navigateByUrl('notification/new/' + this.model.id);
    }

    public suggestDeletion() {
        this.changeSvc.suggestDeletion(this.model).subscribe(() => {
            this.router.navigateByUrl('notification');
        });
    }

    public suggestCreation() {
        this.cardSvc.create(this.model).subscribe(card => {
            console.log('card', card);
            this.changeSvc.suggestCreation(card).subscribe((c) => {
                console.log('c', c);
                this.router.navigateByUrl('notification');
            });
        });
    }

}

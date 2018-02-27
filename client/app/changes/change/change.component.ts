import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ChangeService } from '../services/change.service';
import { CardService } from '../../card/services/card.service';
import { merge, omit } from 'lodash';

@Component({
    selector: 'app-change',
    templateUrl: './change.component.html',
    styleUrls: ['./change.component.scss'],
})
export class ChangeComponent implements OnInit {

    public change;
    public original;
    public suggestion;
    public suggestionImageSrc;
    public loaded = false;

    constructor(private route: ActivatedRoute,
                private router: Router,
                private changeService: ChangeService,
                private cardService: CardService) {
    }

    ngOnInit() {

        if (this.route.snapshot.params['changeId']) {
            this.changeService.getOne(this.route.snapshot.params['changeId']).subscribe(change => {
                console.log('change', change);
                this.change = merge({}, change);
                this.suggestionImageSrc = CardService.getImageLink(this.change.original, 2000);
                this.loaded = true;
            });
        } else if (this.route.snapshot.params['cardId']) {
            this.cardService.getOne(this.route.snapshot.params['cardId']).subscribe(card => {
                this.original = merge({}, card);
                this.suggestion = omit(merge({}, card, {original: card}), 'id');
                this.suggestionImageSrc = CardService.getImageLink(card, 2000);
                this.loaded = true;
            });
        } else {
            this.suggestion = {};
            this.loaded = true;
        }
    }

    public accept() {
        this.changeService.acceptChange(this.change).subscribe(card => {
            this.router.navigateByUrl('card/' + card.id);
        });
    }

    public reject() {
        this.changeService.rejectChange(this.change).subscribe(() => {
            this.router.navigate([
                '..',
                'notification',
            ]);
        });
    }

    public update() {
        this.cardService.create(this.suggestion).subscribe((card: { id }) => {
            this.changeService.suggestUpdate(card).subscribe(() => {
                this.router.navigateByUrl('notification');
            });
        });
    }

    public create() {
        this.cardService.create(this.suggestion).subscribe((card: { id }) => {
            this.changeService.suggestCreation(card).subscribe(() => {
                this.router.navigateByUrl('notification');
            });
        });
    }

}

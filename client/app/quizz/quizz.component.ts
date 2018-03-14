import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CardService } from '../card/services/card.service';
import { FormControl } from '@angular/forms';
import { debounceTime } from 'rxjs/operators';
import { isString, uniq } from 'lodash';

@Component({
    selector: 'app-quizz',
    templateUrl: './quizz.component.html',
    styleUrls: ['./quizz.component.scss'],
})
export class QuizzComponent implements OnInit, OnDestroy {

    private routeParamsSub;
    private formChangeSub;

    public cards: string[] = [];
    public card;
    public imageSrc;
    public currentIndex = 0;
    public attributes;

    public formCtrl: FormControl = new FormControl();

    constructor(private route: ActivatedRoute,
                private cardSvc: CardService) {
    }

    ngOnDestroy() {
        this.routeParamsSub.unsubscribe();
        this.formChangeSub.unsubscribe();
    }

    ngOnInit() {

        this.routeParamsSub = this.route.params.subscribe(params => {
            if (params.cards) {
                this.cards = params.cards.split(',');
                this.getCard(this.cards[0]);
            }
        });

        this.formChangeSub = this.formCtrl.valueChanges.pipe(debounceTime(500)).subscribe(val => {
            this.test(val);
        });
    }

    public getCard(id: string) {
        if (!id) {
            return;
        }

        const index = this.cards.findIndex(c => c === id);
        this.cardSvc.getOne(id).subscribe((card: any) => {
            this.currentIndex = index;
            this.selectCard(card);
        });
    }

    private selectCard(card) {
        this.card = card;
        this.imageSrc = CardService.getImageLink(card, 2000);
        this.attributes = {
            name: false,
            expandedName: false,
            artists: false,
            addition: false,
            technique: false,
            techniqueAuthor: false,
            material: false,
            literature: false,
            table: false,
            institution: false,
            locality: false,
            dating: false,
        };
    }

    public goToNext() {
        this.formCtrl.setValue('');
        const index = this.cards.findIndex(c => c === this.card.id);
        this.getCard(this.cards[index + 1]);
    }

    public getArtistsNames(artists) {
        return artists.map(a => a.name).join(', ');
    }

    private test(formValue) {
        for (const attribute of Object.keys(this.attributes)) {
            const value = this.card[attribute];
            if (attribute === 'institution') {
                this.attributes[attribute] = this.testSingleThesaurus(formValue, value);
            } else if (attribute === 'artists') {
                this.attributes[attribute] = this.testMultipleThesaurus(formValue, value);
            } else if (attribute === 'dating') {
                this.attributes[attribute] = this.testDate(formValue, this.card.datings);
            } else if (isString(value)) {
                this.attributes[attribute] = this.testString(formValue, value);
            }
        }

    }

    private testString(formValue, attributeValue) {
        formValue = this.stripVowelAccent(formValue.toLowerCase()).replace(/\W/g, '');
        attributeValue = this.stripVowelAccent(attributeValue.toLowerCase()).replace(/\W/g, '');
        return formValue.indexOf(attributeValue) > -1;
    }

    private testSingleThesaurus(formValue, attributeValue) {
        if (attributeValue) {
            return this.testString(formValue, attributeValue.name);
        }
    }

    private testMultipleThesaurus(formValue, attributeValue) {
        for (const item of attributeValue) {
            const match = this.testSingleThesaurus(formValue, item);
            if (match) {
                return true;
            }
        }

        return false;
    }

    private testDate(formValue, rules) {

        const years: string[] = uniq(formValue.match(/(-?\d+)/));
        if (years) {
            for (const year of years) {
                const searched = (new Date(year)).getFullYear();
                for (const date of rules) {
                    const from = (new Date(date.from)).getFullYear();
                    const to = (new Date(date.to)).getFullYear();
                    if (searched >= from && searched <= to) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public stripVowelAccent(str) {

        const rExps = [
            {
                re: /[\xC0-\xC6]/g,
                ch: 'A',
            },
            {
                re: /[\xE0-\xE6]/g,
                ch: 'a',
            },
            {
                re: /[\xC8-\xCB]/g,
                ch: 'E',
            },
            {
                re: /[\xE8-\xEB]/g,
                ch: 'e',
            },
            {
                re: /[\xCC-\xCF]/g,
                ch: 'I',
            },
            {
                re: /[\xEC-\xEF]/g,
                ch: 'i',
            },
            {
                re: /[\xD2-\xD6]/g,
                ch: 'O',
            },
            {
                re: /[\xF2-\xF6]/g,
                ch: 'o',
            },
            {
                re: /[\xD9-\xDC]/g,
                ch: 'U',
            },
            {
                re: /[\xF9-\xFC]/g,
                ch: 'u',
            },
            {
                re: /[\xD1]/g,
                ch: 'N',
            },
            {
                re: /[\xF1]/g,
                ch: 'n',
            },
        ];

        for (let i = 0, len = rExps.length; i < len; i++) {
            str = str.replace(rExps[i].re, rExps[i].ch);
        }

        return str;
    }
}

import { Component, Input, OnChanges, OnInit, ViewChild } from '@angular/core';
import { MatAutocompleteTrigger, MatChipInputEvent } from '@angular/material';
import { ENTER } from '@angular/cdk/keycodes';
import { FormControl } from '@angular/forms';
import { IncrementSubject } from '../../services/increment-subject';
import { QueryRef } from 'apollo-angular';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { Literal } from '../../types';
import { filter, map, sampleTime } from 'rxjs/operators';
import { isObject } from 'lodash';
import { Observable } from 'rxjs/Observable';

@Component({
    selector: 'app-thesaurus',
    templateUrl: './thesaurus.component.html',
    styleUrls: ['./thesaurus.component.scss'],
})
export class ThesaurusComponent implements OnInit, OnChanges {

    @ViewChild(MatAutocompleteTrigger) public autocomplete: MatAutocompleteTrigger;
    @ViewChild('input') input;

    @Input() service;
    @Input() placeholder;
    @Input() model: any[] = [];

    public formCtrl: FormControl = new FormControl();

    private queryRef: QueryRef<any>;

    public loading = false;

    /**
     * Default page size
     * @type {number}
     */
    private pageSize = 10;

    /**
     * Init search options
     * @type {IncrementSubject}
     */
    private options: IncrementSubject;

    /**
     * Number of items not shown in result list
     * Shows a message after list if positive
     */
    public moreNbItems = 0;

    /**
     * Filter options debounced.
     * Used by the query
     */
    private optionsFiltered: BehaviorSubject<Literal>;

    public suggestions: Literal[] = [];
    public suggestionsObs: Observable<any>;

    constructor() {
    }

    public separatorKeysCodes = [
        ENTER,
    ];

    public addOnBlur = true;

    ngOnInit() {

        const options = {
            filters: {
                search: null,
            },
            pagination: {
                pageIndex: 0,
                pageSize: 10,
            },
        };

        this.options = new IncrementSubject(options);
        this.optionsFiltered = new BehaviorSubject<Literal>(options);

        // Debounce search...
        // ...and filter only string search terms (when an item is selected, the value is an object)
        this.options.pipe(sampleTime(400), filter(val => {
            return !isObject(val.filters.search); // prevent to emit when value is an object
        })).subscribe(data => {
            this.optionsFiltered.next(data);
        });
    }

    ngOnChanges() {
        for (let i = 0; i < this.model.length; i++) {
            this.model[i] = this.model[i].name;
        }
    }

    public onFocus() {
        this.startSearch();
    }

    open() {
        this.autocomplete.openPanel();
    }

    public startSearch() {

        /**
         * Start search only once
         */
        if (this.queryRef) {
            return;
        }

        this.loading = true;

        this.optionsFiltered.subscribe((data) => {
            this.addOnBlur = false;
            if (data.filters.search) {
                this.loading = true;
            }
        });

        // Init query
        this.queryRef = this.service.watchAll(this.optionsFiltered);

        // When query results arrive, start loading, and count items
        this.queryRef.valueChanges.subscribe((data: any) => {
            this.loading = false;

            const nbTotal = data.length;
            const nbListed = Math.min(data.length, this.pageSize);
            this.moreNbItems = nbTotal - nbListed;
        });

        this.suggestionsObs = this.queryRef.valueChanges.pipe(map((data: any) => {
            this.suggestions = data.items.filter(item => this.model.findIndex(term => term === item.name));
            if (this.suggestions.length === 0) {
                this.addOnBlur = true;
            }
            return this.suggestions;
        }));
    }

    private addTerm(term: string) {
        this.autocomplete.closePanel();
        const indexOf = this.model.findIndex(item => item === term);
        if (term && indexOf === -1) {
            this.model.push(term.trim());
        }
    }

    public addChip(event: MatChipInputEvent): void {
        const term = event.value.trim();
        this.addTerm(term);
        const input = event.input;
        if (input) {
            input.value = '';
        }
    }

    public selectSuggestion(event) {
        this.addTerm(event.option.value.name);
    }

    public removeChip(term: any): void {
        const index = this.model.indexOf(term);

        if (index >= 0) {
            this.model.splice(index, 1);
        }
    }

    public search(ev) {
        const options = {filters: {search: ev}};
        this.options.patch(options);
    }
}

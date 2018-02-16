import { Component, Input, OnChanges, OnInit, ViewChild } from '@angular/core';
import { MatAutocompleteTrigger } from '@angular/material';
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

    @Input() readonly: boolean;
    @Input() service;
    @Input() placeholder;
    @Input() model: any[] = [];

    public formCtrl: FormControl = new FormControl();
    private queryRef: QueryRef<any>;

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

    public suggestionsObs: Observable<any>;

    constructor() {
    }

    ngOnInit() {
        this.convertModel();
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
        this.convertModel();
        if (this.readonly) {
            this.formCtrl.disable();
        } else {
            this.formCtrl.enable();
        }
    }

    public onClick() {
        this.autocomplete.openPanel();
    }

    /**
     * Start search only when focusing on the input
     */
    public startSearch() {

        /**
         * Start search only once
         */
        if (this.queryRef) {
            return;
        }

        // Init query
        this.queryRef = this.service.watchAll(this.optionsFiltered);

        // When query results arrive, start loading, and count items
        this.queryRef.valueChanges.subscribe((data: any) => {
            const nbTotal = data.length;
            const nbListed = Math.min(data.length, this.pageSize);
            this.moreNbItems = nbTotal - nbListed;
        });

        this.suggestionsObs = this.queryRef.valueChanges.pipe(map((data: any) => {
            return data.items.filter(item => this.model.findIndex(term => term === item.name));
        }));
    }

    /**
     * Add term to list
     * Grants unicity of elements.
     * Always close the panel (without resetting results)
     * @param {string} term
     */
    private addTerm(term: string) {
        this.autocomplete.closePanel();
        const indexOf = this.model.findIndex(item => item === term);
        if (term && indexOf === -1) {
            this.model.push(term.trim());
        }
    }

    public onSearch(ev) {
        this.options.patch({filters: {search: ev}});
    }

    /**
     * Add a term
     * On enter key, find if there is an active (focused) option in the mat-select).
     * If not add the term as is. If it does, add the selected option.
     * @param event
     */
    public onEnter(event) {
        if (!this.autocomplete.activeOption) {
            this.addTerm(event.target.value);
            event.target.value = '';
        } else {
            this.addTerm(this.autocomplete.activeOption.value.name);
        }
    }

    /**
     * When click on a suggestion
     * @param event
     */
    public selectSuggestion(event) {
        this.addTerm(event.option.value.name);
    }

    public removeTerm(term: string): void {
        const index = this.model.indexOf(term);
        if (index >= 0) {
            this.model.splice(index, 1);
        }
    }

    /**
     * Convert [{name: 'Yippi Kay yay'}] to ['Yippi Kay yay'].
     * Affects the original object
     */
    private convertModel() {
        for (let i = 0; i < this.model.length; i++) {
            this.model[i] = this.model[i].name ? this.model[i].name : this.model[i];
        }
    }
}

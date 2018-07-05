import { ChangeDetectionStrategy, Component, EventEmitter, Input, OnInit, Output, ViewChild } from '@angular/core';
import { MatAutocompleteTrigger, MatDialog } from '@angular/material';
import { FormControl } from '@angular/forms';
import { IncrementSubject } from '../../services/increment-subject';
import { QueryRef } from 'apollo-angular';
import { BehaviorSubject, Observable } from 'rxjs';
import { Literal } from '../../types';
import { filter, map, sampleTime } from 'rxjs/operators';
import { clone, isArray, isObject, merge } from 'lodash';

@Component({
    selector: 'app-thesaurus',
    templateUrl: './thesaurus.component.html',
    styleUrls: ['./thesaurus.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ThesaurusComponent implements OnInit {

    @ViewChild(MatAutocompleteTrigger) public autocomplete: MatAutocompleteTrigger;
    @ViewChild('input') input;

    @Input() readonly = false;
    @Input() service;
    @Input() placeholder;
    @Input() multiple = true;
    @Input() previewComponent;

    private _model;
    @Input() set model(val) {
        this._model = val;
        this.convertModel();
    }

    @Output() modelChange = new EventEmitter();

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

    public items: { name: string, locality?: string }[] = [];

    constructor(private dialog: MatDialog) {
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

    public openItem(item) {
        this.dialog.open(this.previewComponent, {
            width: '800px',
            data: {item: item},
        }).afterClosed().subscribe(res => {
            merge(item, res);
        });
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
            return data.items.filter(item => this.items.findIndex(term => term.name === item.name));
        }));
    }

    /**
     * Add term to list
     * Grants unicity of elements.
     * Always close the panel (without resetting results)
     * @param {string} term
     */
    private addTerm(term: { name }) {
        this.autocomplete.closePanel();
        const indexOf = this.items.findIndex(item => item.name === term.name);
        if (term && indexOf === -1) {
            if (!this.multiple) {
                this.items.length = 0;
            }
            this.items.push(clone(term)); // clone to get rid of readonly
            this.notifyModel();
        }
    }

    public removeTerm(term: Literal): void {
        const index = this.items.findIndex(item => item.name === term.name);
        if (index >= 0) {
            this.items.splice(index, 1);
            this.notifyModel();
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
            this.addTerm({name: event.target.value});
            event.target.value = '';
        } else {
            this.addTerm(this.autocomplete.activeOption.value);
        }
    }

    /**
     * When click on a suggestion
     * @param event
     */
    public selectSuggestion(event) {
        this.addTerm(event.option.value);
    }

    private notifyModel() {
        if (!this.multiple) {
            this.modelChange.emit(this.items[0] ? this.items[0].name : null);
        } else {
            this.modelChange.emit(this.items.map(v => v.name));
        }
    }

    /**
     * Convert [{name: 'Yippi Kay yay'}] to ['Yippi Kay yay'].
     * Affects the original object
     */
    private convertModel() {
        if (!this.multiple && isObject(this._model)) {
            this.items = [this._model];
            this.notifyModel();
        } else if (this.multiple && isArray(this._model)) {
            this.items = this._model;
            this.notifyModel();
        }

    }
}

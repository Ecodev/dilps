import { Component, ContentChild, EventEmitter, Input, OnInit, Output, TemplateRef } from '@angular/core';
import { IncrementSubject } from '../../services/increment-subject';
import { Observable } from 'rxjs/Observable';
import { FormControl } from '@angular/forms';
import { isObject, merge } from 'lodash';
import { debounceTime, filter, map } from 'rxjs/operators';

/**
 * Default usage:
 * <app-select [service]="amazingSvcInstance" [(model)]="amazingModel" (modelChange)=amazingChangeFn($event)></app-select>
 *
 * Custom template usage :
 * <app-select [service]="svc" [(model)]="model">
 *     <ng-template let-item="item">
 *         <span>{{ item.xxx }}</span>
 *     </ng-template>
 * </app-select>
 *
 * [(model)] and (modelChange) are optional
 *
 * Placeholder :
 * <app-select placeholder="amazing placeholder">
 */
@Component({
    selector: 'app-select',
    templateUrl: './select.component.html',
})
export class SelectComponent implements OnInit {

    @ContentChild(TemplateRef) itemTemplate: TemplateRef<any>;

    /**
     * Service with watchAll function that accepts queryVariables.
     */
    @Input() service;
    @Input() placeholder: string;

    /**
     * The filter to update when searching for a term
     * @type {string}
     */
    @Input() searchField = 'search';

    /**
     * Additionnal filters for query
     */
    @Input() filters: any = {};

    /**
     * Function to customize the rendering of the selected item as text in input
     */
    @Input() displayWith;

    /**
     * Model ouput
     * Usage : (modelChange)
     * @type {EventEmitter<any>}
     */
    @Output() modelChange = new EventEmitter();

    /**
     * On selection change
     * Usage (change)="myFunction($event)"
     * @type {EventEmitter<any>}
     */
    @Output() change = new EventEmitter();

    /**
     * Allow to select a model
     * Usage : [model]
     * @param val
     */
    @Input()
    set model(val) {
        if (this.selected && !val) {
            this.clear(true);
        } else if (!this.selected && val) {
            this.selected = val;
        }
    }

    /**
     * Current selection
     */
    public selected;

    /**
     * Items returned by server to show in listing
     */
    public items: Observable<any[]>;

    /**
     * Nb of shown items in list
     */
    public nbListed: number;

    /**
     * Nb of total elements that correspond to search
     */
    public nbTotal: number;

    public formCtrl: FormControl = new FormControl();
    public loading = false;
    public ac;
    private query: Observable<any>;

    /**
     * Default page size
     * @type {number}
     */
    private pageSize = 10;

    /**
     * Init search options
     * @type {IncrementSubject}
     */
    public options;

    constructor() {

    }

    ngOnInit() {

        // Grants given service has a watchAll function/
        // Todo : Could perform better tests to grant the service accepts observable queryVariables as first paremeter
        if (typeof this.service.watchAll !== 'function') {
            throw new TypeError('Provided service does not contain watchAll function');
        }

        const options = {
            filters: {},
            pagination: {
                pageIndex: 0,
                pageSize: this.pageSize,
            },
        };

        options.filters[this.searchField] = null;
        this.options = new IncrementSubject(options);
        let test = merge({}, options);

        // Debounce search...
        // ...and filter only string search terms (when an item is selected, the value is an object)
        const newSearchObs = this.options.pipe(debounceTime(400), filter((val: any) => {
            return !isObject(val.filters[this.searchField]);
        }), map(val => {
            test = merge({}, this.filters, val);
            return merge({}, this.filters, val);
        }));

        // Init query
        this.query = this.service.watchAll(newSearchObs);

        // When debounced options is fired, start loading (apollo starts query)
        newSearchObs.subscribe((data) => this.loading = true);

        // When query results arrive, start loading, and count items
        this.query.subscribe(data => {
            this.loading = false;
            this.nbTotal = data.length;
            this.nbListed = Math.min(data.length, this.pageSize);
            if (data.length === 1) {
                this.selected = data.items[0];
                this.notify(data.items[0]);
            }
        });

        this.items = this.query.pipe(map(data => data.items));

    }

    public notify(ev) {
        this.modelChange.emit(ev && ev.option ? ev.option.value : ev);
        this.change.emit(ev && ev.option ? ev.option.value : ev);
    }

    /**
     * Transform an object into the string to display in the input (managed by displayWith attribute on autocomplete element)
     * @param item
     * @returns {string}
     */
    public displayFn(item) {
        return item ? item.name : '';
    }

    public clear(preventNotify = false) {

        const options = {filters: {}};
        options.filters[this.searchField] = null;
        this.options.patch(options);

        // Empty input
        this.formCtrl.setValue(null);

        // Notify change
        if (!preventNotify) {
            this.notify(null);
        }
    }

    public search(ev) {
        this.selected = ev;

        const options = {filters: {}};
        options.filters[this.searchField] = ev;
        this.options.patch(options);
    }

}

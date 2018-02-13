import { Component, ContentChild, ElementRef, EventEmitter, Input, OnInit, Output, TemplateRef, ViewChild, } from '@angular/core';
import { IncrementSubject } from '../../services/increment-subject';
import { Observable } from 'rxjs/Observable';
import { FormControl } from '@angular/forms';
import { isObject, merge } from 'lodash';
import { filter, map, sampleTime } from 'rxjs/operators';
import { QueryRef } from 'apollo-angular';
import { MatAutocompleteTrigger } from '@angular/material';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { Literal } from '../../types';

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
 *
 * Never float placeholder :
 * <app-select placeholder="amazing placeholder" floatPlaceholder="never">
 */
@Component({
    selector: 'app-select',
    templateUrl: './select.component.html',
})
export class SelectComponent implements OnInit {

    @ViewChild(MatAutocompleteTrigger) autoTrigger: MatAutocompleteTrigger;
    @ViewChild('input') input: ElementRef;
    @ContentChild(TemplateRef) itemTemplate: TemplateRef<any>;

    /**
     * Service with watchAll function that accepts queryVariables.
     */
    @Input() service;
    @Input() placeholder: string;
    @Input() floatPlaceholder: string = null;
    @Input() readonly = false;

    /**
     * The filter attribute to bind when searching for a term
     * @type {string}
     */
    @Input() searchField = 'search';

    /**
     * Whether to show the search icon
     */
    @Input() showIcon = true;

    /**
     * Additional filters for query
     */
    @Input() filters: any = {};

    /**
     * Function to customize the rendering of the selected item as text in input
     */
    @Input() displayWith: (any) => string;

    /**
     * Whether the disabled can be changed
     */
    @Input() set disabled(disabled: boolean) {
        disabled ? this.formCtrl.disable() : this.formCtrl.enable();
    }

    /**
     * Model output
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

    @Output() blur = new EventEmitter();

    /**
     * Allow to select a model
     * Usage : [model]
     * @param val
     */
    @Input() set model(val) {
        this.selected = val;
    }

    /**
     * Current selection
     */
    public selected;

    /**
     * Items returned by server to show in listing
     */
    public items: Observable<any[]>;

    public formCtrl: FormControl = new FormControl();
    public loading = false;
    public ac;
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

    constructor() {
    }

    public onFocus() {
        this.startSearch();
    }

    open() {
        this.autoTrigger.openPanel();
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
        this.optionsFiltered = new BehaviorSubject<Literal>(options);

        // Debounce search...
        // ...and filter only string search terms (when an item is selected, the value is an object)
        this.options.pipe(sampleTime(400), filter(val => {
            return !isObject(val.filters[this.searchField]); // prevent to emit when value is an object
        })).subscribe(data => {
            this.optionsFiltered.next(merge({}, {filters: this.filters}, data));
        });

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
            if (data.filters[this.searchField]) {
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

            if (data.length === 1) {
                this.selected = data.items[0];
                this.notify(data.items[0]);
            }
        });

        this.items = this.queryRef.valueChanges.pipe(map((data: any) => data.items));
    }

    public notify(ev) {
        this.modelChange.emit(ev && ev.option ? ev.option.value : ev);
        this.change.emit(ev && ev.option ? ev.option.value : ev);
    }

    public getDisplayFn(): (item: any) => string {
        if (this.displayWith) {
            return this.displayWith;
        }

        return (item) => item ? item.name : '';
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

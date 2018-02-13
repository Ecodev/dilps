import { Component, Input, ViewEncapsulation } from '@angular/core';

@Component({
    selector: 'app-table-button',
    templateUrl: './table-button.component.html',
    styleUrls: ['./table-button.component.scss'],
    encapsulation: ViewEncapsulation.None,
})
export class TableButtonComponent {

    @Input() queryParams: any;
    @Input() queryParamsHandling: any;
    @Input() label: string;
    @Input() icon: string;
    @Input() href: string;
    @Input() navigate: string;

    constructor() {
    }

}

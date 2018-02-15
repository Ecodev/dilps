import { Component, EventEmitter, Input, OnChanges, OnInit, Output } from '@angular/core';
import { MatChipInputEvent } from '@angular/material';
import { COMMA, ENTER } from '@angular/cdk/keycodes';

@Component({
    selector: 'app-thesaurus',
    templateUrl: './thesaurus.component.html',
    styleUrls: ['./thesaurus.component.scss'],
})
export class ThesaurusComponent implements OnInit, OnChanges {

    @Input() placeholder;
    @Input() model: string[] = [];
    @Output() modelChange = new EventEmitter();

    constructor() {
    }

    public separatorKeysCodes = [
        ENTER,
        COMMA,
    ];

    ngOnInit() {
    }

    ngOnChanges() {
        this.model = this.model.map((v: any) => v.name ? v.name : v);
    }

    add(event: MatChipInputEvent): void {
        const input = event.input;
        const term = event.value.trim();

        const indexOf = this.model.findIndex(item => item === term);
        if (term && indexOf === -1) {
            this.model.push(term.trim());
        }

        if (input) {
            input.value = '';
        }

        this.modelChange.emit(this.model);
    }

    remove(term: any): void {
        const index = this.model.indexOf(term);

        if (index >= 0) {
            this.model.splice(index, 1);
        }

        this.modelChange.emit(this.model);
    }

}

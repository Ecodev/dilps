import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
    selector: 'app-dialog-footer',
    templateUrl: './dialog-footer.component.html',
    styleUrls: ['./dialog-footer.component.scss'],
})
export class DialogFooterComponent {
    @Input() canCreate;
    @Input() item;
    @Output() create = new EventEmitter();
    @Output() update = new EventEmitter();
    @Output() delete = new EventEmitter();
}

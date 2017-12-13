import { Injectable } from '@angular/core';
import { MatDialog, MatSnackBar } from '@angular/material';
import { ConfirmComponent } from '../components/confirm/confirm.component';

@Injectable()
export class AlertService {

    constructor(private dialog: MatDialog, private snackBar: MatSnackBar) {
    }

    public info(message) {
        this.snackBar.open(message, null, {
            duration: 1500,
            verticalPosition: 'bottom',
            horizontalPosition: 'start',
        });
    }

    public error(message) {
        this.snackBar.open(message, null, {
            duration: 1500,
            extraClasses: ['snackbar-error'],
            verticalPosition: 'bottom',
            horizontalPosition: 'start',
        });
    }

    public confirm(title, message, confirmText, cancelText = 'Annuler') {

        const dialog = this.dialog.open(ConfirmComponent, {
            data: {
                title: title,
                message: message,
                confirmText: confirmText,
                cancelText: cancelText,
            },
        });

        return dialog.afterClosed();
    }
}

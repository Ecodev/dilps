import { Component, Inject } from '@angular/core';
import { AlertService } from '../../shared/components/alert/alert.service';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { AbstractDetail } from '../../shared/components/AbstractDetail';
import { ArtistComponent } from '../../artists/artist/artist.component';
import { InstitutionService } from '../../institutions/services/institution.service';
import { UserService } from '../services/user.service';
import { AbstractControl, FormControl, FormGroup, ValidationErrors } from '@angular/forms';
import { UserType } from '../../shared/generated-types';

function matchPassword(ac: AbstractControl): ValidationErrors | null {
    const password = ac.get('password').value; // to get value in input tag
    const passwordConfirmation = ac.get('passwordConfirmation').value; // to get value in input tag

    if (password !== passwordConfirmation) {
        ac.get('passwordConfirmation').setErrors({password: true});
    }

    return null;
}

@Component({
    selector: 'app-profile',
    templateUrl: './user.component.html',
})
export class UserComponent extends AbstractDetail {

    public roles = [];

    public passwordGroupCtrl: FormGroup;
    public passwordCtrl: FormControl;
    public passwordConfirmationCtrl: FormControl;

    public institution;

    constructor(public institutionService: InstitutionService,
                service: UserService,
                alertSvc: AlertService,
                userSvc: UserService,
                dialogRef: MatDialogRef<ArtistComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, userSvc, data);

        this.roles = service.getRoles();

        this.passwordCtrl = new FormControl('');
        this.passwordConfirmationCtrl = new FormControl('');
        this.passwordGroupCtrl = new FormGroup(
            {
                password: this.passwordCtrl,
                passwordConfirmation: this.passwordConfirmationCtrl,
            },
            {
                updateOn: 'change',
                validators: [matchPassword],
            });

    }

    public isShibbolethUser() {
        return this.data.item.type === UserType.unil;
    }

    protected postQuery() {
        this.institution = this.data.item.institution;
    }

    protected postUpdate(model) {
        this.institution = model.institution;
    }

}

import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { CollectionService } from '../services/collection.service';
import { AlertService } from '../../shared/components/alert/alert.service';
import { InstitutionService } from '../../institutions/services/institution.service';
import { AbstractDetail } from '../../shared/components/AbstractDetail';
import { UserService } from '../../users/services/user.service';
import { CollectionVisibility, UserRole } from '../../shared/generated-types';
import { findKey } from 'lodash';

@Component({
    selector: 'app-collection',
    templateUrl: './collection.component.html',
})
export class CollectionComponent extends AbstractDetail implements OnInit {

    public visibility = 1;
    public visibilities = {
        1: {
            value: CollectionVisibility.private,
            text: 'par moi',
            color: null,
        },
        2: {
            value: CollectionVisibility.administrator,
            text: 'par moi et les admins',
            color: 'accent',
        },
        3: {
            value: CollectionVisibility.member,
            text: 'par tout le monde',
            color: 'primary',
        },
    };

    constructor(public institutionSvc: InstitutionService,
                service: CollectionService,
                userSvc: UserService,
                alertSvc: AlertService,
                dialogRef: MatDialogRef<CollectionComponent>,
                @Inject(MAT_DIALOG_DATA) data: any) {

        super(service, alertSvc, dialogRef, userSvc, data);
    }

    protected postQuery() {
        // Init visibility
        this.visibility = +findKey(this.visibilities, (s) => {
            return s.value === this.data.item.visibility;
        });
    }

    public updateVisibility(ev) {
        this.data.item.visibility = this.visibilities[ev.value].value;
    }

    /**
     * Visibility slider can always be seen by creator or by admins if visibility is accessible to administrators/members
     * @returns {boolean}
     */
    public showVisibility() {

        // While no user loaded
        if (!this.user) {
            return false;
        }

        const collectionIsNotPrivate = this.data.item.visibility === CollectionVisibility.administrator ||
                                       this.data.item.visibility === CollectionVisibility.member;

        const hasCreator = !!this.data.item.creator;
        const isCreator = hasCreator && this.user.id === this.data.item.creator.id;

        const adminCanView = this.user.role === UserRole.administrator && (collectionIsNotPrivate || !hasCreator);
        return isCreator || adminCanView;
    }
}

import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MatDialog } from '@angular/material';
import { AbstractList } from '../../shared/components/AbstractList';
import { UserService } from '../services/user.service';
import { UserComponent } from '../user/user.component';

@Component({
    selector: 'app-users',
    templateUrl: './users.component.html',
    styleUrls: ['./users.component.scss'],
})
export class UsersComponent extends AbstractList implements OnInit {

    constructor(service: UserService,
                router: Router,
                route: ActivatedRoute,
                dialog: MatDialog) {

        super('users', service, UserComponent, router, route, dialog);

    }
}

import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/observable/of';
import { CreateUserMutation, DeleteUserMutation, UpdateUserMutation, UserQuery, UsersQuery, } from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { createUserMutation, deleteUserMutation, updateUserMutation, userQuery, usersQuery } from './userQueries';

@Injectable()
export class UserService extends AbstractModelService<UserQuery['user'],
    UsersQuery['users'],
    CreateUserMutation['createUser'],
    UpdateUserMutation['updateUser'],
    DeleteUserMutation['deleteUser']> {

    private demoUser = {
        id: '1',
        login: 'sbaptista',
        firstname: 'Samuel',
        lastname: 'Baptista',
    };

    constructor(apollo: Apollo, private router: Router) {
        super(apollo, 'user', userQuery, usersQuery, createUserMutation, updateUserMutation, deleteUserMutation);
    }

    public getCurrentUser(): Observable<any> {
        const user = JSON.parse(localStorage.getItem('dilps-user'));
        return Observable.of(user);
    }

    public login(loginData): Observable<any> {
        localStorage.setItem('dilps-user', JSON.stringify(this.demoUser));
        return Observable.of(1);
    }

    public logout(): void {
        localStorage.removeItem('dilps-user');
        this.router.navigate(['/login']);
    }

}

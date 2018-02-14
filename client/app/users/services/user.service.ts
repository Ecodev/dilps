import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/observable/of';
import { CreateUserMutation, DeleteUserMutation, UpdateUserMutation, UserQuery, UsersQuery, UserType, } from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import { createUserMutation, deleteUserMutation, updateUserMutation, userQuery, usersQuery } from './userQueries';
import { map } from 'rxjs/operators';

@Injectable()
export class UserService extends AbstractModelService<UserQuery['user'],
    UsersQuery['users'],
    CreateUserMutation['createUser'],
    UpdateUserMutation['updateUser'],
    DeleteUserMutation['deleteUser']> {

    private currentUser;

    constructor(apollo: Apollo, private router: Router) {
        super(apollo, 'user', userQuery, usersQuery, createUserMutation, updateUserMutation, deleteUserMutation);
    }

    public getEmptyObject() {
        return {
            login: '',
            email: '',
            activeUntil: '',
            termsAgreement: null,
            type: UserType.default,
            institution: null,
        };
    }

    public getCurrentUser(): Observable<any> {

        // todo : replace by real login behavior
        const id = localStorage.getItem('dilps-userId');
        if (!id) {
            return Observable.of(null);
        }

        if (this.currentUser) {
            return Observable.of(this.currentUser);
        }

        return this.getOne(id).pipe(map(user => {
            this.currentUser = user;
            return user;
        }));

    }

    public login(loginData): Observable<any> {
        // todo : replace by real login behavior
        localStorage.setItem('dilps-userId', '2');
        return this.getCurrentUser();
    }

    public logout(): void {
        // todo : replace by real login behavior
        localStorage.removeItem('dilps-userId');
        this.router.navigate(['/login']);
    }

}

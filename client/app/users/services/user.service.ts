import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/observable/of';
import { Subject } from 'rxjs/Subject';
import {
    CreateUserMutation,
    DeleteUsersMutation,
    LoginMutation,
    LogoutMutation,
    UpdateUserMutation,
    UserInput,
    UserQuery,
    UserRole,
    UsersQuery,
    UserType,
    ViewerQuery,
} from '../../shared/generated-types';
import { AbstractModelService } from '../../shared/services/abstract-model.service';
import {
    createUserMutation,
    deleteUsersMutation,
    loginMutation,
    logoutMutation,
    updateUserMutation,
    userQuery,
    usersQuery,
    viewerQuery,
} from './userQueries';
import { map } from 'rxjs/operators';
import { merge } from 'lodash';

@Injectable()
export class UserService extends AbstractModelService<UserQuery['user'],
    UsersQuery['users'],
    CreateUserMutation['createUser'],
    UpdateUserMutation['updateUser'],
    DeleteUsersMutation['deleteUsers']> {

    constructor(apollo: Apollo, private router: Router) {
        super(apollo, 'user', userQuery, usersQuery, createUserMutation, updateUserMutation, deleteUsersMutation);
    }

    public getEmptyObject(): UserInput {
        return {
            login: '',
            email: '',
            activeUntil: '',
            termsAgreement: null,
            type: UserType.default,
            role: UserRole.student,
            institution: null,
        };
    }

    public getCurrentUser(): Observable<ViewerQuery['viewer']> {
        return this.apollo.query<ViewerQuery>({
            query: viewerQuery,
            fetchPolicy: 'network-only',
        }).pipe(map(result => {
            const user: any = result.data ? result.data.viewer : null;
            return merge({}, user, {roleLevel: this.getRole(user.role).level});
        }));
    }

    public getRole(role) {
        const roles = this.getRoles();
        return roles.find(r => r.name === role);
    }

    public getRoles() {
        return [
            {
                name: UserRole.student,
                text: 'Etudiant',
                level: 1,
            },
            {
                name: UserRole.junior,
                text: 'Junior',
                level: 2,
            },
            {
                name: UserRole.senior,
                text: 'Senior',
                level: 3,
            },
            {
                name: UserRole.administrator,
                text: 'Admin',
                level: 4,
            },
        ];
    }

    public login(loginData): Observable<LoginMutation['login']> {
        return this.apollo.mutate<LoginMutation>({
            mutation: loginMutation,
            variables: loginData,
        }).pipe(map(result => result.data.login));
    }

    public logout(): Observable<LogoutMutation['logout']> {
        const subject = new Subject<LogoutMutation['logout']>();

        this.router.navigate(['/login'], {queryParams: {logout: true}}).then(() => {
            this.apollo.mutate<LogoutMutation>({
                mutation: logoutMutation,
            }).pipe(map(result => result.data.logout)).subscribe((v) => (this.apollo.getClient().resetStore() as Promise<null>).then(() => {
                subject.next(v);
            }));
        });

        return subject;
    }

    public hasTempAccess() {
        return sessionStorage.getItem('tempAccess') === 'true';
    }

    public startTempAccess() {
        sessionStorage.setItem('tempAccess', 'true');
        this.router.navigateByUrl('/');
    }

    public revokeTempAccess() {
        sessionStorage.removeItem('tempAccess');
        this.router.navigateByUrl('login');
    }

}

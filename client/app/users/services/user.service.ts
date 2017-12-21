import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { createUserMutation, deleteUserMutation, updateUserMutation, userQuery, usersQuery } from '../../shared/queries/user';
import 'rxjs/add/observable/of';
import { filter, map } from 'rxjs/operators';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';

@Injectable()
export class UserService {

    private demoUser = {
        id: '1',
        login: 'sbaptista',
        firstname: 'Samuel',
        lastname: 'Baptista',
    };

    constructor(private apollo: Apollo, private router: Router) {
    }

    public watchAll(variables: BehaviorSubject<any>): Observable<any> {

        const query = this
            .apollo
            .watchQuery({
                query: usersQuery,
                variables: variables.getValue(),
                fetchPolicy: 'cache-and-network',
            });

        variables.subscribe(vars => {
            query.setVariables(vars);
        });

        return query
            .valueChanges
            .pipe(filter((data: any) => !!data.data), map((data: any) => {
                return data.data.users;
            }));
    }

    public getOne(id): Observable<any> {
        return this
            .apollo
            .query({
                query: userQuery,
                variables: {
                    id: id,
                },
            })
            .pipe(map((data: any) => {
                return data.data.user;
            }));
    }

    public getCurrentUser(): Observable<any> {

        const user = JSON.parse(localStorage.getItem('dilps-user'));
        return Observable.of(user);

        // return this.apollo
        //            .query({query: currentUserForProfileQuery})
        //            .pipe(map(({data: {viewer}}: any) => viewer));
    }

    public login(loginData): Observable<any> {

        // Be sure to destroy all Apollo data, before changing user
        // this.apollo.getClient().resetStore();
        //
        // return this.apollo.mutate({
        //     mutation: loginMutation,
        //     variables: loginData,
        // });

        localStorage.setItem('dilps-user', JSON.stringify(this.demoUser));

        return Observable.of(1);
    }

    public logout(): void {

        // this.apollo
        //     .mutate({
        //         mutation: logoutMutation,
        //     })
        //     .subscribe(() => {
        //         this.apollo.getClient().resetStore();
        //         this.router.navigate(['/login']);
        //         this.themeSvc.set(null);
        //     });

        localStorage.removeItem('dilps-user');
        this.router.navigate(['/login']);
    }

    public create(user: any): Observable<any> {

        return this.apollo.mutate({
            mutation: createUserMutation,
            variables: {
                input: user,
            },
        }).pipe(map(({data: {createUser}}: any) => createUser));

    }

    public update(user): Observable<any> {

        return this.apollo.mutate({
            mutation: updateUserMutation,
            variables: {
                id: user.id,
                input: {
                    email: user.email,
                    login: user.login,
                    activeUntil: user.activeUntil,
                    isAdministrator: user.idAdministrator,
                    termsAgreement: user.termsAgreement,
                    type: user.type,
                },
            },
        });
    }

    public delete(user: any): Observable<any> {
        return this.apollo.mutate({
            mutation: deleteUserMutation,
            variables: {
                id: user.id,
            },
        });
    }
}

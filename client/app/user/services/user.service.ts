import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { Router } from '@angular/router';
import { Observable } from 'rxjs/Observable';
import { updateUserMutation } from '../../shared/queries/user';
import { ThemeService } from '../../shared/services/theme.service';
import 'rxjs/add/observable/of';

@Injectable()
export class UserService {

    private demoUser = {
        id: '1',
        login: 'sbaptista',
        firstname: 'Samuel',
        lastname: 'Baptista',
    };

    constructor(private apollo: Apollo, private router: Router, private themeSvc: ThemeService) {
    }

    public getCurrentUser(): Observable<any> {

        const user = localStorage.getItem('dilps-user');
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

    public update(user) {

        // Extract just the fields we need
        const partialUser = (({id, firstname, lastname}: any) => ({
            id,
            firstname,
            lastname,
        }))(user);

        this.apollo.mutate({
            mutation: updateUserMutation,
            variables: {user: partialUser},
        });
    }

}

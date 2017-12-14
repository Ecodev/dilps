import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './shared/services/auth.guard';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { UserComponent } from './users/user/user.component';
import { ListComponent } from './list/list.component';
import { ImageComponent } from './image/image.component';
import { UsersComponent } from './users/users/users.component';
import { CollectionsComponent } from './collections/collections.component';
import { UserResolver } from './users/services/user.resolver';
import { ImageResolver } from './image/services/image.resolver';

export const routes: Routes = [

    {
        path: 'login',
        component: LoginComponent,
    },

    // Auth required routes
    {
        path: '',
        component: HomeComponent,
        canActivate: [AuthGuard],
        children: [
            {
                path: '',
                component: ListComponent,
                data: {showLogo: true},
            },
            {
                path: 'image/:imageId',
                component: ImageComponent,
                resolve: {image: ImageResolver},

            },
            {
                path: 'profile',
                component: UserComponent,
            },
            {
                path: 'user',
                children: [
                    {
                        path: '',
                        component: UsersComponent,
                    },
                    {
                        path: 'new',
                        component: UserComponent,
                    },
                    {
                        path: ':userId',
                        component: UserComponent,
                        resolve: {user: UserResolver},
                    },
                ],
            },
            {
                path: 'collection',
                component: CollectionsComponent,
                children: [
                    {
                        path: ':collectionId',
                        component: ListComponent,
                        data: {showLogo: false},
                    },
                ],
            },
        ],
    },

];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
})
export class AppRoutingModule {
}

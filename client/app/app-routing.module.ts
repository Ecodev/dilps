import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './shared/services/auth.guard';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { UserComponent } from './users/user/user.component';
import { ListComponent } from './list/list.component';
import { CardComponent } from './card/card.component';
import { UsersComponent } from './users/users/users.component';
import { CollectionsComponent } from './collections/collections/collections.component';
import { CardResolver } from './card/services/card.resolver';
import { CollectionComponent } from './collections/collection/collection.component';
import { InstitutionsComponent } from './institutions/institutions/institutions.component';
import { ArtistsComponent } from './artists/artists/artists.component';
import { ChangesComponent } from './changes/changes/changes.component';
import { ChangeComponent } from './changes/change/change.component';

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
                path: 'card/:cardId',
                component: CardComponent,
                resolve: {card: CardResolver},
                data: {showLogo: true},

            },
            {
                path: 'profile',
                component: UserComponent,
            },
            {
                path: 'user',
                component: UsersComponent,
            },
            {
                path: 'institution',
                component: InstitutionsComponent,
            },
            {
                path: 'artist',
                component: ArtistsComponent,
            },
            {
                path: 'notification',
                component: ChangesComponent,
            },
            {
                path: 'notification/new/:cardId',
                component: ChangeComponent,
            },
            {
                path: 'notification/:changeId',
                component: ChangeComponent,
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
            {
                path: 'my-collection',
                component: CollectionsComponent,
                children: [
                    {
                        path: ':collectionId',
                        component: ListComponent,
                        data: {showLogo: false},
                        children: [
                            {
                                path: 'edit',
                                component: CollectionComponent,
                            },
                        ],
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

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
import { InstitutionsComponent } from './institutions/institutions/institutions.component';
import { ArtistsComponent } from './artists/artists/artists.component';
import { ChangesComponent } from './changes/changes/changes.component';
import { ChangeComponent } from './changes/change/change.component';
import { UserResolver } from './users/services/user.resolver';
import { AuthAdminGuard } from './shared/services/auth.admin.guard';
import { QuizzComponent } from './quizz/quizz.component';
import { CollectionVisibility, UserRole } from './shared/generated-types';

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
        resolve: {user: UserResolver},
        children: [
            {
                path: '',
                component: ListComponent,
                data: {showLogo: true},
            },
            {
                path: 'card/new',
                component: CardComponent,
                data: {showLogo: true},
                runGuardsAndResolvers: 'always',
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
                canActivate: [AuthAdminGuard],
            },
            {
                path: 'institution',
                component: InstitutionsComponent,
                canActivate: [AuthAdminGuard],
            },
            {
                path: 'artist',
                component: ArtistsComponent,
                canActivate: [AuthAdminGuard],
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
                path: 'quizz',
                component: QuizzComponent,
            },
            {
                path: 'collection',
                component: CollectionsComponent,
                data: {
                    editionButtonsForRoles : [UserRole.administrator, UserRole.senior],
                    filters: {
                        isSource: false,
                        visibilities: [CollectionVisibility.administrator, CollectionVisibility.member]
                    },
                },
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
                resolve: {creator: UserResolver},
                data: {
                    canCreate: true,
                    showLogo: false,
                    showUnclassified: true,
                    showMyCards: true,
                    filters: {
                        isSource: false,
                    },
                },
                children: [
                    {
                        path: '',
                        component: ListComponent,
                        data: {
                            filters: {collections: []},
                        },
                    },
                    {
                        path: 'my-cards',
                        component: ListComponent,
                        resolve: {creator: UserResolver},
                    },
                    {
                        path: ':collectionId',
                        component: ListComponent,
                    },
                ],
            },
            {
                path: 'source',
                component: CollectionsComponent,
                canActivate: [AuthAdminGuard],
                data: {
                    canCreate: true,
                    filters: {
                        isSource: true,
                    },
                },
                children: [
                    {
                        path: ':collectionId',
                        component: ListComponent,
                        data: {
                            showLogo: false,
                            filters: {},
                        },
                    },
                ],
            },
        ],
    },

];

@NgModule({
    imports: [
        RouterModule.forRoot(routes, {
            paramsInheritanceStrategy: 'emptyOnly',
        }),
    ],
    exports: [RouterModule],
})
export class AppRoutingModule {
}

import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './shared/services/auth.guard';
import { LoginComponent } from './login/login.component';
import { HomeComponent } from './home/home.component';
import { UserComponent } from './user/user/user.component';
import { ListComponent } from './list/list.component';
import { ImageComponent } from './image/image.component';

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
            },
            {
                path: 'image/:imageId',
                component: ImageComponent,
            },
            {
                path: 'user',
                component: UserComponent,
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

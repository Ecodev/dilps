import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { Apollo, ApolloModule } from 'apollo-angular';
import { HttpLink, HttpLinkModule } from 'apollo-angular-link-http';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {
    MatButtonModule, MatCheckboxModule, MatIconModule, MatInputModule, MatListModule, MatSidenavModule, MatSnackBarModule, MatToolbarModule,
} from '@angular/material';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './shared/services/auth.guard';
import { UserComponent } from './user/user/user.component';
import { ThemeService } from './shared/services/theme.service';
import { HomeComponent } from './home/home.component';
import { NgProgressModule } from 'ngx-progressbar';
import { BootLoaderComponent } from './shared/components/boot-loader/boot-loader.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NetworkActivityService } from './shared/services/network-activity.service';
import { UserService } from './user/services/user.service';
import { FlexLayoutModule } from '@angular/flex-layout';
import { ListComponent } from './list/list.component';

import { NaturalGalleryModule } from 'angular-natural-gallery';
import { ImageComponent } from './image/image.component';

@NgModule({
    declarations: [
        AppComponent,
        LoginComponent,
        UserComponent,
        HomeComponent,
        BootLoaderComponent,
        ListComponent,
        ImageComponent,
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        HttpClientModule,
        FormsModule,
        ReactiveFormsModule,
        ApolloModule,
        HttpLinkModule,
        BrowserAnimationsModule,
        MatButtonModule,
        MatCheckboxModule,
        MatIconModule,
        NgProgressModule,
        PerfectScrollbarModule,
        MatInputModule,
        MatSnackBarModule,
        FlexLayoutModule,
        MatSidenavModule,
        MatListModule,
        MatToolbarModule,
        NaturalGalleryModule,
    ],
    providers: [
        AuthGuard,
        ThemeService,
        NetworkActivityService,
        UserService,
    ],
    bootstrap: [AppComponent],
})
export class AppModule {
    constructor(apollo: Apollo, httpLink: HttpLink) {
        apollo.create({
            link: httpLink.create({uri: '/graphql'}),
            cache: new InMemoryCache(),
        });
    }

}

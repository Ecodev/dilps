import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { Apollo, ApolloModule } from 'apollo-angular';
import { ApolloLink, concat } from 'apollo-link';
import { createUploadLink } from 'apollo-upload-client';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import {
    MatAutocompleteModule,
    MatButtonModule,
    MatCheckboxModule,
    MatDatepickerModule,
    MatDialogModule,
    MatIconModule,
    MatInputModule,
    MatListModule,
    MatMenuModule,
    MatNativeDateModule,
    MatPaginatorModule,
    MatProgressSpinnerModule,
    MatSidenavModule,
    MatSliderModule,
    MatSlideToggleModule,
    MatSnackBarModule,
    MatTableModule,
    MatTabsModule,
    MatToolbarModule,
    MatTooltipModule,
} from '@angular/material';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';

import { FlexLayoutModule } from '@angular/flex-layout';
import { NgProgressModule } from 'ngx-progressbar';

import { AppRoutingModule } from './app-routing.module';
import { AuthGuard } from './shared/services/auth.guard';
import { ThemeService } from './shared/services/theme.service';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NetworkActivityService } from './shared/services/network-activity.service';
import { UserService } from './users/services/user.service';

import { AppComponent } from './app.component';
import { UserComponent } from './users/user/user.component';
import { HomeComponent } from './home/home.component';
import { LoginComponent } from './login/login.component';
import { BootLoaderComponent } from './shared/components/boot-loader/boot-loader.component';
import { ListComponent } from './list/list.component';
import { ImageComponent } from './image/image.component';
import { NaturalGalleryModule } from 'angular-natural-gallery';
import { UsersComponent } from './users/users/users.component';
import { TableButtonComponent } from './shared/components/table-button/table-button.component';
import { CollectionsComponent } from './collections/collections/collections.component';
import { UserResolver } from './users/services/user.resolver';
import { ConfirmComponent } from './shared/components/confirm/confirm.component';
import { AlertService } from './shared/services/alert.service';
import { FocusDirective } from './shared/directives/focus';
import { ImageResolver } from './image/services/image.resolver';
import { ImageService } from './image/services/image.service';
import { CollectionComponent } from './collections/collection/collection.component';
import { CollectionService } from './collections/services/collection.service';
import { SelectComponent } from './shared/components/select/select.component';
import { InstitutionService } from './shared/services/institution.service';

@NgModule({
    declarations: [
        AppComponent,
        LoginComponent,
        UserComponent,
        HomeComponent,
        BootLoaderComponent,
        ListComponent,
        ImageComponent,
        UsersComponent,
        TableButtonComponent,
        CollectionsComponent,
        ConfirmComponent,
        FocusDirective,
        CollectionComponent,
        SelectComponent
    ],
    entryComponents: [
        ConfirmComponent,
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        HttpClientModule,
        FormsModule,
        ReactiveFormsModule,
        ApolloModule,
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
        MatTableModule,
        MatProgressSpinnerModule,
        MatPaginatorModule,
        MatMenuModule,
        MatDialogModule,
        MatTooltipModule,
        MatSlideToggleModule,
        MatSliderModule,
        MatTabsModule,
        MatAutocompleteModule,
        MatDatepickerModule,
        MatNativeDateModule,
    ],
    providers: [
        AuthGuard,
        ThemeService,
        NetworkActivityService,
        CollectionService,
        UserService,
        UserResolver,
        ImageResolver,
        ImageService,
        AlertService,
        InstitutionService,
    ],
    bootstrap: [AppComponent],
})
export class AppModule {
    constructor(apollo: Apollo, networkActivitySvc: NetworkActivityService) {
        const link = createUploadLink({uri: '/graphql'});

        const middleware = new ApolloLink((operation, forward) => {
            networkActivitySvc.increase();
            return forward(operation).map(response => {
                networkActivitySvc.updateErrors(response);
                networkActivitySvc.decrease();
                return response;
            });
        });

        apollo.create({
            link: concat(middleware, link),
            cache: new InMemoryCache(),
        });
    }

}

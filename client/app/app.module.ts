import { AgmCoreModule } from '@agm/core';
import { HttpClientModule } from '@angular/common/http';
import { NgModule } from '@angular/core';

import { FlexLayoutModule } from '@angular/flex-layout';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MatAutocompleteModule } from '@angular/material/autocomplete';
import { MatButtonModule } from '@angular/material/button';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatChipsModule } from '@angular/material/chips';
import { DateAdapter, MatNativeDateModule } from '@angular/material/core';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatDialogModule } from '@angular/material/dialog';
import { MatExpansionModule } from '@angular/material/expansion';
import { MAT_FORM_FIELD_DEFAULT_OPTIONS, MatFormFieldDefaultOptions, MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatListModule } from '@angular/material/list';
import { MatMenuModule } from '@angular/material/menu';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSelectModule } from '@angular/material/select';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatSlideToggleModule } from '@angular/material/slide-toggle';
import { MatSliderModule } from '@angular/material/slider';
import { MatSnackBarModule } from '@angular/material/snack-bar';
import { MatTableModule } from '@angular/material/table';
import { MatTabsModule } from '@angular/material/tabs';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatTooltipModule } from '@angular/material/tooltip';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { NaturalGalleryModule } from '@ecodev/angular-natural-gallery';
import { NaturalIconModule, NaturalSearchModule } from '@ecodev/natural';
import { NgProgressModule } from '@ngx-progressbar/core';
import { ngfModule } from 'angular-file';
import { Apollo, ApolloModule } from 'apollo-angular';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloLink } from 'apollo-link';
import { onError } from 'apollo-link-error';
import { createUploadLink } from 'apollo-upload-client';
import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';

import { AppRoutingModule } from './app-routing.module';

import { AppComponent } from './app.component';
import { ArtistComponent } from './artists/artist/artist.component';
import { ArtistsComponent } from './artists/artists/artists.component';
import { ArtistService } from './artists/services/artist.service';
import { CardComponent } from './card/card.component';
import { CardResolver } from './card/services/card.resolver';
import { CardService } from './card/services/card.service';
import { ChangeComponent } from './changes/change/change.component';
import { ChangesComponent } from './changes/changes/changes.component';
import { ChangeService } from './changes/services/change.service';
import { CollectionComponent } from './collections/collection/collection.component';
import { CollectionsComponent } from './collections/collections/collections.component';
import { CollectionService } from './collections/services/collection.service';
import { HomeComponent } from './home/home.component';
import { InstitutionComponent } from './institutions/institution/institution.component';
import { InstitutionsComponent } from './institutions/institutions/institutions.component';
import { InstitutionService } from './institutions/services/institution.service';
import { ListComponent } from './list/list.component';
import { LoginComponent } from './login/login.component';
import { TermsAgreementComponent } from './login/terms-agreement.component';
import { QuizzComponent } from './quizz/quizz.component';
import { NumberSelectorComponent } from './quizz/shared/number-selector/number-selector.component';
import { AddressComponent } from './shared/components/address/address.component';
import { AlertService } from './shared/components/alert/alert.service';
import { ConfirmComponent } from './shared/components/alert/confirm.component';
import { BootLoaderComponent } from './shared/components/boot-loader/boot-loader.component';
import { CollectionSelectorComponent } from './shared/components/collection-selector/collection-selector.component';
import { DialogFooterComponent } from './shared/components/dialog-footer/dialog-footer.component';
import { DownloadComponent } from './shared/components/download/download.component';
import { MassEditComponent } from './shared/components/mass-edit/mass-edit.component';
import { RelationsComponent } from './shared/components/relations/relations.component';
import { SelectComponent } from './shared/components/select/select.component';
import { StampComponent } from './shared/components/stamp/stamp.component';
import { TableButtonComponent } from './shared/components/table-button/table-button.component';
import { ThesaurusComponent } from './shared/components/thesaurus/thesaurus.component';
import { apolloDefaultOptions } from './shared/config/apollo.default.options';
import { FileDropDirective } from './shared/directives/file-drop.directive';
import { FocusDirective } from './shared/directives/focus';
import { RolePipe } from './shared/pipes/role.pipe';
import { TypePipe } from './shared/pipes/type.pipe';
import { AuthAdminGuard } from './shared/services/auth.admin.guard';
import { AuthGuard } from './shared/services/auth.guard';
import { LinkMutationService } from './shared/services/link-mutation.service';
import { NetworkActivityService } from './shared/services/network-activity.service';
import { ThemeService } from './shared/services/theme.service';
import { UploadService } from './shared/services/upload.service';
import { UserResolver } from './users/services/user.resolver';
import { UserService } from './users/services/user.service';
import { UserComponent } from './users/user/user.component';
import { UsersComponent } from './users/users/users.component';

/** Custom options to configure the form field's look and feel */
const formFieldDefaults: MatFormFieldDefaultOptions = {
    appearance: 'legacy',
};

@NgModule({
    declarations: [
        AppComponent,
        LoginComponent,
        UserComponent,
        HomeComponent,
        BootLoaderComponent,
        ListComponent,
        CardComponent,
        UsersComponent,
        TableButtonComponent,
        CollectionsComponent,
        ConfirmComponent,
        FocusDirective,
        CollectionComponent,
        SelectComponent,
        TermsAgreementComponent,
        InstitutionsComponent,
        InstitutionComponent,
        ArtistComponent,
        ArtistsComponent,
        ThesaurusComponent,
        AddressComponent,
        DialogFooterComponent,
        StampComponent,
        ChangesComponent,
        ChangeComponent,
        FileDropDirective,
        CollectionSelectorComponent,
        DownloadComponent,
        RolePipe,
        TypePipe,
        QuizzComponent,
        NumberSelectorComponent,
        MassEditComponent,
        RelationsComponent,
    ],
    entryComponents: [
        ConfirmComponent,
        UserComponent,
        TermsAgreementComponent,
        InstitutionComponent,
        ArtistComponent,
        CollectionSelectorComponent,
        DownloadComponent,
        CollectionComponent,
        NumberSelectorComponent,
        MassEditComponent,
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
        MatSelectModule,
        MatFormFieldModule,
        FlexLayoutModule,
        MatSidenavModule,
        MatListModule,
        MatToolbarModule,
        NaturalGalleryModule,
        NaturalSearchModule,
        MatTableModule,
        MatProgressSpinnerModule,
        MatPaginatorModule,
        MatMenuModule,
        MatDialogModule,
        MatTooltipModule,
        MatExpansionModule,
        MatSlideToggleModule,
        MatSliderModule,
        MatTabsModule,
        MatAutocompleteModule,
        MatDatepickerModule,
        MatNativeDateModule,
        MatChipsModule,
        ngfModule,
        NaturalIconModule.forRoot({}),
        AgmCoreModule.forRoot({
            apiKey: 'AIzaSyBIBMlG6xXDmpPERQgKdo_Dwhtz5SX5dto',
            libraries: ['places'],
        }),
    ],
    providers: [
        AuthGuard,
        AuthAdminGuard,
        ThemeService,
        NetworkActivityService,
        CollectionService,
        UserService,
        UserResolver,
        CardResolver,
        CardService,
        AlertService,
        InstitutionService,
        ArtistService,
        ChangeService,
        UploadService,
        LinkMutationService,
        {provide: MAT_FORM_FIELD_DEFAULT_OPTIONS, useValue: formFieldDefaults},
    ],
    bootstrap: [AppComponent],
})
export class AppModule {
    constructor(apollo: Apollo,
                networkActivitySvc: NetworkActivityService,
                alertSvc: AlertService,
                private dateAdapter: DateAdapter<Date>) {

        dateAdapter.setLocale('fr-ch');

        const link = createUploadLink({
            uri: '/graphql',
            credentials: 'include',
        });

        const middleware = new ApolloLink((operation, forward) => {
            networkActivitySvc.increase();
            return forward(operation).map(response => {
                networkActivitySvc.decrease();
                return response;
            });
        });

        const errorLink = onError(({graphQLErrors, networkError}) => {

            // Network errors seems not to be catched by above middleware, and we need to be informed to decrease pending queries
            if (networkError) {
                alertSvc.error('Une erreur est survenue sur le réseau');
                networkActivitySvc.decrease();
            }

            // Graphql responses with errors are valid responses and are catched by the above middleware.
            // There seems to be no need to do something here
            // Seems we have no need to deal
            if (graphQLErrors) {
                alertSvc.error('Une erreur est survenue du côté du serveur');
                networkActivitySvc.updateErrors(graphQLErrors);
            }
        });

        apollo.create({
            link: middleware.concat(errorLink).concat(link),
            cache: new InMemoryCache(),
            defaultOptions: apolloDefaultOptions,
        });
    }

}

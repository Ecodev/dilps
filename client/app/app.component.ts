import { Component, HostBinding, OnInit } from '@angular/core';
import { Apollo } from 'apollo-angular';
import gql from 'graphql-tag';
import { ThemeService } from './shared/services/theme.service';
import { OverlayContainer } from '@angular/cdk/overlay';

// We use the gql tag to parse our query string into a query document
const Users = gql`
  query Users {
    users {
      items {
        id
        login
      }
    }
  }
`;

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
})
export class AppComponent implements OnInit {

    users: any;

    /**
     * Bind theme at root-app level
     * @type {string}
     */
    @HostBinding('class') public theme = '';

    /**
     * When first route is loaded, hide the app-bootloader component
     */
    public initialized: boolean;

    constructor(private themeSvc: ThemeService, private overlayContainer: OverlayContainer, apollo: Apollo) {

    }

    public ngOnInit() {

        // this.apollo.watchQuery<any>({
        //     query: Users,
        // })
        //     .valueChanges
        //     .subscribe(({data}) => {
        //         this.users = data.users;
        //     });

        this.themeSvc.theme.subscribe(newTheme => {

            // Remove old theme class from overlay (dialogs, snackbars, etc...)
            this.themeSvc.themes.forEach(them => {
                this.overlayContainer.getContainerElement().classList.remove(them);
                this.overlayContainer.getContainerElement().classList.remove(them + 'Dark');
            });

            // set new theme class
            this.overlayContainer.getContainerElement().classList.add(newTheme);
            this.theme = newTheme;
        });

    }
}

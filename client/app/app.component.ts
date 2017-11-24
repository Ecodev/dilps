import { Component, OnInit } from '@angular/core';
import { Apollo } from 'apollo-angular';
import gql from 'graphql-tag';

// We use the gql tag to parse our query string into a query document
const Users = gql`
  query Users {
    users {
      items {
        id
        name
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
    title = 'DILPS';
    users: any;

    constructor(private apollo: Apollo) {
    }

    ngOnInit() {
        this.apollo.watchQuery<any>({
            query: Users,
        })
            .valueChanges
            .subscribe(({data}) => {
                this.users = data.users;
            });
    }
}

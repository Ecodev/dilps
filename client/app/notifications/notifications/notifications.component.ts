import { Component, OnInit } from '@angular/core';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { PaginatedDataSource } from '../../shared/services/paginated.data.source';
import { Observable } from 'rxjs/Observable';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
    selector: 'app-notifications',
    templateUrl: './notifications.component.html',
    styleUrls: ['./notifications.component.scss'],
})
export class NotificationsComponent implements OnInit {

    public displayedColumns = [
        'name',
    ];

    public dataSource;
    private listingOptions = new IncrementSubject({});

    constructor(private router: Router,
                private route: ActivatedRoute) {
    }

    ngOnInit() {

        this.dataSource = new PaginatedDataSource(
            Observable.of({
                items: [],
                length: 0,
            }),
            this.listingOptions,
            {},
            true,
            this.router,
            this.route,
            'artists',
        );
    }

}

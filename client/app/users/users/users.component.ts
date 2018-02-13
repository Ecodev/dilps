import { Component, OnInit } from '@angular/core';
import { UserService } from '../services/user.service';
import { PaginatedDataSource } from '../../shared/services/paginated.data.source';
import { ActivatedRoute, Router } from '@angular/router';
import { IncrementSubject } from '../../shared/services/increment-subject';

@Component({
    selector: 'app-users',
    templateUrl: './users.component.html',
    styleUrls: ['./users.component.scss'],
})
export class UsersComponent implements OnInit {

    public displayedColumns = [
        'name',
        'email',
        'activeUntil',
        'termsAgreement',
    ];
    public dataSource;
    private listingOptions = new IncrementSubject({});

    constructor(private userSvc: UserService, private router: Router, private route: ActivatedRoute) {
    }

    ngOnInit() {

        const queryRef = this.userSvc.watchAll(this.listingOptions);
        this.dataSource = new PaginatedDataSource(
            queryRef.valueChanges,
            this.listingOptions,
            {},
            true,
            this.router,
            this.route,
            'users',
        );
    }

}

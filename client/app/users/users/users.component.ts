import { Component, OnInit } from '@angular/core';
import { UserService } from '../services/user.service';
import { PaginatedDataSource } from '../../shared/services/paginated.data.source';
import { ActivatedRoute, Router } from '@angular/router';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { MatDialog } from '@angular/material';
import { UserComponent } from '../user/user.component';

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

    constructor(private userSvc: UserService, private router: Router, private route: ActivatedRoute, private dialog: MatDialog) {
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

    public edit(user) {

        const dialogRef = this.dialog.open(UserComponent, {
            width: '800px',
            data: {user: user},
        });

        dialogRef.afterClosed().subscribe(data => {
            // if returned data is null, it means deletion
            if (data === null) {
                this.router.navigate(['..'], {relativeTo: this.route.firstChild});
            }
        });
    }

    public add() {
        const dialogRef = this.dialog.open(UserComponent, {
            width: '800px',
        });
    }
}

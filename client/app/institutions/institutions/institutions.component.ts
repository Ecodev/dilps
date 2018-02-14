import { Component, OnInit } from '@angular/core';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { ActivatedRoute, Router } from '@angular/router';
import { MatDialog } from '@angular/material';
import { InstitutionService } from '../services/institution.service';
import { PaginatedDataSource } from '../../shared/services/paginated.data.source';
import { InstitutionComponent } from '../institution/institution.component';

@Component({
    selector: 'app-institutions',
    templateUrl: './institutions.component.html',
    styleUrls: ['./institutions.component.scss'],

})
export class InstitutionsComponent implements OnInit {

    public displayedColumns = [
        'name',
    ];
    public dataSource;
    private listingOptions = new IncrementSubject({});

    constructor(private institutionService: InstitutionService,
                private router: Router,
                private route: ActivatedRoute,
                private dialog: MatDialog) {
    }

    ngOnInit() {

        const queryRef = this.institutionService.watchAll(this.listingOptions);
        this.dataSource = new PaginatedDataSource(
            queryRef.valueChanges,
            this.listingOptions,
            {},
            true,
            this.router,
            this.route,
            'institutions',
        );
    }

    public edit(institution) {

        const dialogRef = this.dialog.open(InstitutionComponent, {
            width: '800px',
            data: {institution: institution},
        });

        dialogRef.afterClosed().subscribe(data => {
            // if returned data is null, it means deletion
            if (data === null) {
                this.router.navigate(['..'], {relativeTo: this.route.firstChild});
            }
        });
    }

    public add() {
        this.dialog.open(InstitutionComponent, {
            width: '800px',
        });
    }

}

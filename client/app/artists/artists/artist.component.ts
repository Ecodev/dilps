import { Component, OnInit } from '@angular/core';
import { IncrementSubject } from '../../shared/services/increment-subject';
import { ActivatedRoute, Router } from '@angular/router';
import { MatDialog } from '@angular/material';
import { ArtistService } from '../services/artist.service';
import { PaginatedDataSource } from '../../shared/services/paginated.data.source';
import { ArtistComponent } from '../artist/artist.component';

@Component({
    selector: 'app-artists',
    templateUrl: './artists.component.html',
    styleUrls: ['./artists.component.scss'],

})
export class ArtistsComponent implements OnInit {

    public displayedColumns = [
        'name',
    ];
    public dataSource;
    private listingOptions = new IncrementSubject({});

    constructor(private artistService: ArtistService,
                private router: Router,
                private route: ActivatedRoute,
                private dialog: MatDialog) {
    }

    ngOnInit() {

        const queryRef = this.artistService.watchAll(this.listingOptions);
        this.dataSource = new PaginatedDataSource(
            queryRef.valueChanges,
            this.listingOptions,
            {},
            true,
            this.router,
            this.route,
            'artists',
        );
    }

    public edit(artist) {

        const dialogRef = this.dialog.open(ArtistComponent, {
            width: '800px',
            data: {artist: artist},
        });

        dialogRef.afterClosed().subscribe(data => {
            // if returned data is null, it means deletion
            if (data === null) {
                this.router.navigate(['..'], {relativeTo: this.route.firstChild});
            }
        });
    }

    public add() {
        this.dialog.open(ArtistComponent, {
            width: '800px',
        });
    }

}

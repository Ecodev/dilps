import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ChangeService } from '../services/change.service';

@Component({
    selector: 'app-change',
    templateUrl: './change.component.html',
    styleUrls: ['./change.component.scss'],
})
export class ChangeComponent implements OnInit {

    public change;

    constructor(private route: ActivatedRoute, private router: Router, private changeService: ChangeService) {
    }

    ngOnInit() {
        if (this.route.snapshot.params['changeId']) {
            this.changeService.getOne(this.route.snapshot.params['changeId']).subscribe(change => {
                this.change = change;
            });
        }
    }

    public accept() {
        this.changeService.acceptChange(this.change).subscribe(card => {
            this.router.navigateByUrl('card/' + card.id);
        });
    }

    public reject() {
        this.changeService.rejectChange(this.change).subscribe((changed: boolean) => {
            console.log('changed', changed);
            this.router.navigate(['..', 'notifications']);
        });
    }

}

import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';
import { UserService } from '../user/services/user.service';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
    providers: [],
})
export class HomeComponent implements OnInit {

    public initialized;

    constructor(private themeSvc: ThemeService, private userSvc: UserService) {
    }

    ngOnInit() {
    }

}

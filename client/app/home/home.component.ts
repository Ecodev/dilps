import { Component, OnInit } from '@angular/core';
import { ThemeService } from '../shared/services/theme.service';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
    providers: [],
})
export class HomeComponent implements OnInit {

    constructor(private themeSvc: ThemeService) {
    }

    ngOnInit() {
    }

    toggleTheme() {
        this.themeSvc.toggle();
    }

}

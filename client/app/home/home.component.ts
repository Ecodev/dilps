import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { SidemenuService } from '../shared/services/sidemenu.service';
import { ThemeService } from '../shared/services/theme.service';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
    providers: [
        // NavigationService,
        SidemenuService,
    ],
})
export class HomeComponent implements OnInit, AfterViewInit {

    /**
     * Fix for refresh sidenav resize
     * Todo : Remove when https://github.com/angular/material2/issues/6743 is fixed
     * Todo : Remove ngAfterViewInit function
     * Todo : Removce #navContainer in template
     */
    @ViewChild('navContainer') private navContainer;

    constructor(public sidemenuSvc: SidemenuService, private themeSvc: ThemeService) {
    }

    ngOnInit() {
    }

    /**
     * Fix for refresh sidenav resize
     * Todo : Remove when https://github.com/angular/material2/issues/6743 is fixed
     * Todo : Remove @ViewChild('navContainer') private navContainer too
     * Todo : Remove #navContainer in template
     */
    ngAfterViewInit() {
        this.navContainer._ngZone.onMicrotaskEmpty.subscribe(() => {
            this.navContainer._updateContentMargins();
            this.navContainer._changeDetectorRef.markForCheck();
        });
    }

    toggleTheme() {
        this.themeSvc.toggle();
    }

}

import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { FormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { UserComponent } from './user.component';
import { ThemeService } from '../../shared/services/theme.service';
import { MatButtonToggleModule, MatCheckboxModule, MatIconModule, MatInputModule, MatMenuModule } from '@angular/material';
import { BehaviorSubject } from 'rxjs/BehaviorSubject';
import { UserService } from '../services/user.service';
import { FlexLayoutModule } from '@angular/flex-layout';
import { LangSelectorComponent } from '../../lang/lang-selector/lang-selector.component';

// https://stackoverflow.com/questions/39825600/angular-2-unit-testing-cant-resolve-all-parameters-for-router
const mockRouter = {
    navigate: jasmine.createSpy('navigate'),
};

class ThemeServiceStub {
    get theme() {
        return new BehaviorSubject<any>('default');
    }
}

class UserServiceStub {
}

describe('UserComponent', () => {
    let component: UserComponent;
    let fixture: ComponentFixture<UserComponent>;

    beforeEach(async(() => {
        TestBed.configureTestingModule({
            declarations: [
                UserComponent,
                LangSelectorComponent,
            ],
            imports: [
                MatInputModule,
                MatButtonToggleModule,
                MatIconModule,
                FormsModule,
                RouterTestingModule,
                BrowserAnimationsModule,
                MatCheckboxModule,
                FlexLayoutModule,
                MatMenuModule,
            ],
            providers: [
                {
                    provide: ThemeService,
                    useClass: ThemeServiceStub,
                },
                {
                    provide: UserService,
                    useClass: UserServiceStub,
                },
            ],
        }).compileComponents();
    }));

    beforeEach(() => {
        fixture = TestBed.createComponent(UserComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});

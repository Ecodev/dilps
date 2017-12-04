import { inject, TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { UserService } from './user.service';
import { ThemeService } from '../../shared/services/theme.service';
import { DummyServices } from '../../shared/testing/DummyServices';

describe('UserService', () => {
    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                ThemeService,
                {
                    provide: UserService,
                    useClass: DummyServices,
                },
            ],
        });
    });

    it('should create', inject([UserService], (service: UserService) => {
        expect(service).toBeTruthy();
    }));
});

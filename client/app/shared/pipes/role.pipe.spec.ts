import { RouterTestingModule } from '@angular/router/testing';
import { UserService } from '../../users/services/user.service';
import { UserRole } from '../generated-types';
import { MockApolloProvider } from '../testing/MockApolloProvider';
import { RolePipe } from './role.pipe';
import { inject, TestBed } from '@angular/core/testing';

describe('RolePipe', () => {

    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                {
                    provide: RolePipe,
                    useClass: RolePipe,
                },
                {
                    provide: UserService,
                    useClass: UserService,
                },
                MockApolloProvider,
            ],
        });
    });

    it('create an instance', inject([RolePipe], (pipe: RolePipe) => {
        expect(pipe).toBeTruthy();
        expect(pipe.transform(UserRole.student)).toBe('Etudiant');
        expect(pipe.transform('non-existing-role')).toBe('');
    }));
});

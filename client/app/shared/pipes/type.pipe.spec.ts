import { RouterTestingModule } from '@angular/router/testing';
import { UserService } from '../../users/services/user.service';
import { UserType } from '../generated-types';
import { MockApolloProvider } from '../testing/MockApolloProvider';
import { TypePipe } from './type.pipe';
import { inject, TestBed } from '@angular/core/testing';

describe('TypePipe', () => {

    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                {
                    provide: TypePipe,
                    useClass: TypePipe,
                },
                {
                    provide: UserService,
                    useClass: UserService,
                },
                MockApolloProvider,
            ],
        });
    });

    it('create an instance', inject([TypePipe], (pipe: TypePipe) => {
        expect(pipe).toBeTruthy();
        expect(pipe.transform(UserType.default)).toBe('Externe');
        expect(pipe.transform(UserType.unil)).toBe('AAI');
        expect(pipe.transform(UserType.legacy)).toBe('Legacy');
        expect(pipe.transform('non-existing-type')).toBe('');
    }));
});

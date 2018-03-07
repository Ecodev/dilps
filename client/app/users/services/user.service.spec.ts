import { TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { MockApolloProvider } from '../../shared/testing/MockApolloProvider';
import { AbstractModelServiceSpec } from '../../shared/testing/AbstractModelServiceSpec';
import { userMetaTesting } from '../../shared/testing/userMetaTesting';
import { UserService } from './user.service';
import { UserType } from '../../shared/generated-types';

describe('UserService', () => {

    const expectedOne = {
        id: '456',
        email: 'test string',
        login: 'test string',
        role: 'student',
        termsAgreement: '2018-01-18T11:43:31',
        type: UserType.default,
        activeUntil: '2018-01-18T11:43:31',
        institution: {
            id: '456',
            name: 'test string',
            __typename: 'Institution',
        },
        __typename: 'User',
        creationDate: '2018-01-18T11:43:31',
        creator: userMetaTesting,
        updateDate: '2018-01-18T11:43:31',
        updater: userMetaTesting,
        permissions: {
            update: true,
            delete: true,
            __typename : 'Permissions'
        },
    };

    const expectedOneForAll = {
        id: '456',
        email: 'test string',
        login: 'test string',
        activeUntil: '2018-01-18T11:43:31',
        termsAgreement: '2018-01-18T11:43:31',
        role: 'student',
        __typename: 'User',
    };

    const expectedAll = {
        items: [
            expectedOneForAll,
            expectedOneForAll,
        ],
        pageSize: 1,
        pageIndex: 1,
        length: 1,
        __typename: 'UserPagination',
    };

    const expectedCreate = {
        id: '456',
        creationDate: '2018-01-18T11:43:31',
        creator: userMetaTesting,
        __typename: 'User',
    };

    const expectedUpdate = {
        id: 123,
        updateDate: '2018-01-18T11:43:31',
        updater: userMetaTesting,
        __typename: 'User',
    };

    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                UserService,
                MockApolloProvider,
            ],
        });
    });

    AbstractModelServiceSpec.test(UserService, expectedOne, expectedAll, expectedCreate, expectedUpdate);

});

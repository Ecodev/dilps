import { TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { MockApolloProvider } from '../../shared/testing/MockApolloProvider';
import { AbstractModelServiceSpec } from '../../shared/testing/AbstractModelServiceSpec';
import { userMetaTesting } from '../../shared/testing/userMetaTesting';
import { ArtistService } from './artist.service';

describe('ArtistService', () => {

    const expectedOne = {
        id: '456',
        name: 'test string',
        __typename: 'Artist',
        creationDate: '2018-01-18T11:43:31',
        creator: userMetaTesting,
        updateDate: '2018-01-18T11:43:31',
        updater: userMetaTesting,
    };

    const expectedOneForAll = {
        id: '456',
        name: 'test string',
        __typename: 'Artist',
    };

    const expectedAll = {
        items: [
            expectedOneForAll,
            expectedOneForAll,
        ],
        pageSize: 1,
        pageIndex: 1,
        length: 1,
        __typename: 'ArtistPagination',
    };

    const expectedCreate = {
        id: '456',
        creationDate: '2018-01-18T11:43:31',
        creator: userMetaTesting,
        __typename: 'Artist',
    };

    const expectedUpdate = {
        updateDate: '2018-01-18T11:43:31',
        updater: userMetaTesting,
        __typename: 'Artist',
    };

    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                ArtistService,
                MockApolloProvider,
            ],
        });
    });

    AbstractModelServiceSpec.test(ArtistService, expectedOne, expectedAll, expectedCreate, expectedUpdate);

});

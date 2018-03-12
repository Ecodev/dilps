import { TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { MockApolloProvider } from '../../shared/testing/MockApolloProvider';
import { AbstractModelServiceSpec } from '../../shared/testing/AbstractModelServiceSpec';
import { userMetaTesting } from '../../shared/testing/userMetaTesting';
import { CollectionService } from './collection.service';
import { LinkMutationService } from '../../shared/services/link-mutation.service';
import { DummyServices } from '../../shared/testing/DummyServices';

describe('CollectionService', () => {

    const expectedOne = {
        id: '456',
        name: 'test string',
        description: 'test string',
        isSource: true,
        sorting: 1,
        visibility: 'private',
        institution: {
            id: '456',
            name: 'test string',
            __typename: 'Institution',
        },
        __typename: 'Collection',
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
        name: 'test string',

        children: [
            {
                id: '456',
                name: 'test string',
                children: [
                    {
                        id: '456',
                        name: 'test string',
                        children: [
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                        ],
                        __typename: 'Collection',
                    },
                    {
                        id: '456',
                        name: 'test string',
                        children: [
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                        ],
                        __typename: 'Collection',
                    },
                ],
                __typename: 'Collection',
            },
            {
                id: '456',
                name: 'test string',
                children: [
                    {
                        id: '456',
                        name: 'test string',
                        children: [
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                        ],
                        __typename: 'Collection',
                    },
                    {
                        id: '456',
                        name: 'test string',
                        children: [
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                            {
                                id: '456',
                                name: 'test string',
                                children: [
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                    {
                                        id: '456',
                                        name: 'test string',
                                        __typename: 'Collection',
                                    },
                                ],
                                __typename: 'Collection',
                            },
                        ],
                        __typename: 'Collection',
                    },
                ],
                __typename: 'Collection',
            },
        ],
        __typename: 'Collection',
    };

    const expectedAll = {
        items: [
            expectedOneForAll,
            expectedOneForAll,
        ],
        pageSize: 1,
        pageIndex: 1,
        length: 1,
        __typename: 'CollectionPagination',
    };

    const expectedCreate = {
        id: '456',
        creationDate: '2018-01-18T11:43:31',
        creator: userMetaTesting,
        __typename: 'Collection',
    };

    const expectedUpdate = {
        id: 123,
        updateDate: '2018-01-18T11:43:31',
        updater: userMetaTesting,
        __typename: 'Collection',
    };

    beforeEach(() => {
        TestBed.configureTestingModule({
            imports: [
                RouterTestingModule,
            ],
            providers: [
                CollectionService,
                MockApolloProvider,
                {
                    provide: LinkMutationService,
                    class: DummyServices,
                },
            ],
        });
    });

    AbstractModelServiceSpec.test(CollectionService, expectedOne, expectedAll, expectedCreate, expectedUpdate);

});

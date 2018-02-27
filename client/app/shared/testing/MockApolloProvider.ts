import { Injectable } from '@angular/core';
import { Apollo } from 'apollo-angular';
import { ApolloClient } from 'apollo-client';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { SchemaLink } from 'apollo-link-schema';
import { buildClientSchema } from 'graphql';
import { addMockFunctionsToSchema } from 'graphql-tools';
import { apolloDefaultOptions } from '../config/apollo.default.options';
import { schema as introspectionResult } from '../../../../data/tmp/schema';

/**
 * A mock Apollo to be used in tests only
 */
@Injectable()
class MockApollo extends Apollo {
    constructor() {
        super();
        const mockClient = this.createMockClient();
        super.setClient(mockClient);
    }

    /**
     * This will create a fake ApolloClient who can responds to queries
     * against our real schema with random values
     *
     * @returns {ApolloClient<any>}
     */
    private createMockClient() {
        const schema = buildClientSchema(introspectionResult.data as any);

        // Configure hardcoded mocked values on a type basis.
        // That means all data will look be very similar, but at least
        // tests are robust and won't change if/when random generators
        // would be used differently
        const mocks = {
            ID: () => '456',
            Int: () => 1,
            Float: () => 0.5,
            String: () => 'test string',
            Boolean: () => true,
            Date: () => '2018-02-27',
            DateTime: () => '2018-01-18T11:43:31',
            Login: () => 'test string',
            CardStatus: () => 'new',
            UserType: () => 'default'
            UserRole: () => 'student'
        };

        addMockFunctionsToSchema({schema, mocks});

        const apolloCache = new InMemoryCache((window as any).__APOLLO_STATE__);

        const graphqlClient = new ApolloClient({
            cache: apolloCache,
            link: new SchemaLink({schema}),
            defaultOptions: apolloDefaultOptions,
        });

        return graphqlClient;
    }
}

/**
 * This is the only way to use our MockApollo
 */
export const MockApolloProvider = {
    provide: Apollo,
    useClass: MockApollo,
};

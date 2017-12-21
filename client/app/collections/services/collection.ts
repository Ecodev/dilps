import gql from 'graphql-tag';

export const collectionsQuery = gql`
query Collections($pagination: PaginationInput) {
    collections(pagination: $pagination) {
        items {
            id
            name
            description
        }
        pageSize
        pageIndex
        length
    }
}`;

export const collectionQuery = gql`
query Collection($id: CollectionID!) {
    collection(id: $id) {
        id
        name
        description
        isSource
        sorting
        institution {
            id
            name
        }
        creationDate
        updateDate
        creator
        {
            id
            login
        }
        updater
        {
            id
            login
        }
    }
}`;

export const createCollectionMutation = gql`
mutation CreateCollection ($input: CollectionInput!) {
    createCollection (input: $input) {
        id
    }
}`;

export const updateCollectionMutation = gql`
mutation UpdateCollection($id: CollectionID!, $input: CollectionInput!) {
    updateCollection(id: $id, input: $input) {
        id
    }
}`;

export const deleteCollectionMutation = gql`
mutation DeleteCollection ($id: CollectionID!){
    deleteCollection(id: $id)
}`;

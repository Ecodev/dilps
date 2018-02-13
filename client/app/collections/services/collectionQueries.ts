import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

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
        creator {
            ...userMeta
        }
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const createCollectionMutation = gql`
mutation CreateCollection ($input: CollectionInput!) {
    createCollection (input: $input) {
        id
        creationDate
        creator {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const updateCollectionMutation = gql`
mutation UpdateCollection($id: CollectionID!, $input: CollectionInput!) {
    updateCollection(id: $id, input: $input) {
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const deleteCollectionMutation = gql`
mutation DeleteCollection ($id: CollectionID!){
    deleteCollection(id: $id)
}`;
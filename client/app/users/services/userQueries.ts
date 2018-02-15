import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

export const usersQuery = gql`
query Users($filters: UserFilter, $pagination: PaginationInput) {
    users(filters: $filters, pagination: $pagination) {
        items {
            id
            email
            login
            activeUntil
        }
        pageSize
        pageIndex
        length
    }
}`;

export const userQuery = gql`
query User($id: UserID!) {
    user(id: $id) {
        id
        email
        login
        activeUntil
        isAdministrator
        termsAgreement
        type
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

export const createUserMutation = gql`
mutation CreateUser ($input: UserInput!) {
    createUser (input: $input) {
        id
        creationDate
        creator {
            ...userMeta
        }
    }
}${userMetaFragment}`;


export const updateUserMutation = gql`
mutation UpdateUser($id: UserID!, $input: UserInput!) {
    updateUser(id: $id, input: $input) {
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;


export const deleteUsersMutation = gql`
mutation DeleteUsers ($ids: [UserID!]!){
    deleteUsers(ids: $ids)
}`;

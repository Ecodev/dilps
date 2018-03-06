import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

const userDetailsFragment = gql`
fragment userDetails on User {
    id
    email
    login
    activeUntil
    role
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
}${userMetaFragment}`;

export const usersQuery = gql`
query Users($filters: UserFilter, $pagination: PaginationInput) {
    users(filters: $filters, pagination: $pagination) {
        items {
            id
            login
            email
            role
            activeUntil
            termsAgreement
        }
        pageSize
        pageIndex
        length
    }
}`;

export const userQuery = gql`
query User($id: UserID!) {
    user(id: $id) {
        ...userDetails
    }
}${userDetailsFragment}`;

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

export const loginMutation = gql`
mutation Login ($login: Login! $password: String!){
    login(login: $login password: $password) {
        ...userDetails
    }
}${userDetailsFragment}`;

export const logoutMutation = gql`
mutation Logout {
    logout
}`;

export const viewerQuery = gql`
query Viewer {
    viewer {
        ...userDetails
    }
}${userDetailsFragment}`;

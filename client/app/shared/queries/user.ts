import gql from 'graphql-tag';

export const usersQuery = gql`
query Users($filters: UserFilter, $pagination: PaginationInput) {
    users(filters: $filters, pagination: $pagination) {
        items {
            id
            email
            login
            organization
            activeUntil
            creationDate
            updateDate
        }
        pageSize
        pageIndex
        length
    }
}`;

export const updateUserMutation = gql`
mutation UpdateUser($user: UpdateUserInput!) {
    updateUser(input:$user) {
        id
        email
        login
        organization
        activeUntil
        creationDate
        updateDate
    }
}`;

export const logoutMutation = gql`
mutation  {
    logout
}`;

export const loginMutation = gql`
mutation Login($login: Login!, $password: String!) {
    login(login:$login, password:$password) {
        id
        email
        login
        organization
        activeUntil
        creationDate
        updateDate
    }
}`;

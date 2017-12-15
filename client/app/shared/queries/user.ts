import gql from 'graphql-tag';

export const usersQuery = gql`
query Users($filters: UserFilter, $pagination: PaginationInput) {
    users(filters: $filters, pagination: $pagination) {
        items {
            id
            email
            login
            activeUntil
            creationDate
            updateDate
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
    }
}`;

export const createUserMutation = gql`
mutation CreateUser ($input: UserInput!) {
    createUser (input: $input) {
        id
    }
}`;

export const updateUserMutation = gql`
mutation UpdateUser($id: UserID!, $input: UserInput!) {
    updateUser(id: $id, input: $input) {
        id
        email
        login
        activeUntil
        creationDate
        updateDate
    }
}`;

export const deleteUserMutation = gql`
mutation DeleteUser ($id: UserID!){
    deleteUser(id: $id)
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
        activeUntil
        creationDate
        updateDate
    }
}`;

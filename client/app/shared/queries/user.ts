import gql from 'graphql-tag';

export const currentUserForProfileQuery = gql`
query CurrentUserForProfile {
    viewer {
        id
        login
        firstname
        lastname
        isActive
        dateCreation
        dateUpdate
    }
}`;

export const usersQuery = gql`
query Users($filters: UserFilter, $pagination: PaginationInput) {
    users(filters: $filters, pagination: $pagination) {
        items {
            id
            firstname
            lastname
            fullName
            isActive
            dateCreation
            dateUpdate
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
        login
        firstname
        lastname
        fullName
        isActive
        dateCreation
        dateUpdate
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
    login
    firstname
    lastname
    isActive
    dateCreation
    dateUpdate
  }
}`;

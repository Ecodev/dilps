import gql from 'graphql-tag';

export const institutionsQuery = gql`
query Institutions($filters: InstitutionFilter, $pagination: PaginationInput) {
    institutions(filters: $filters, pagination: $pagination) {
        items {
            id
            name
        }
        pageSize
        pageIndex
        length
    }
}`;

export const institutionQuery = gql`
query Institution($id: InstitutionID!) {
    institution(id: $id) {
        id
        name
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

export const createInstitutionMutation = gql`
mutation CreateInstitution ($input: InstitutionInput!) {
    createInstitution (input: $input) {
        id
    }
}`;

export const updateInstitutionMutation = gql`
mutation UpdateInstitution($id: InstitutionID!, $input: InstitutionInput!) {
    updateInstitution(id: $id, input: $input) {
        id
    }
}`;

export const deleteInstitutionMutation = gql`
mutation DeleteInstitution ($id: InstitutionID!){
    deleteInstitution(id: $id)
}`;

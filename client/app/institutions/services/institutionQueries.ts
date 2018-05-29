import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

export const institutionsQuery = gql`
query Institutions($filters: OldInstitutionFilter, $pagination: PaginationInput) {
    institutions(filters: $filters, pagination: $pagination) {
        items {
            id
            name
            locality
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
        locality
        street
        postcode
        latitude
        longitude
        creationDate
        country {
            id
            code
            name
        }
        creator {
            ...userMeta
        }
        updateDate
        updater {
            ...userMeta
        }
        permissions {
            update
            delete
        }
    }
}${userMetaFragment}`;

export const createInstitutionMutation = gql`
mutation CreateInstitution ($input: InstitutionInput!) {
    createInstitution (input: $input) {
        id
        creationDate
        creator {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const updateInstitutionMutation = gql`
mutation UpdateInstitution($id: InstitutionID!, $input: InstitutionPartialInput!) {
    updateInstitution(id: $id, input: $input) {
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const deleteInstitutionsMutation = gql`
mutation DeleteInstitutions ($ids: [InstitutionID!]!){
    deleteInstitutions(ids: $ids)
}`;

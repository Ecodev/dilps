import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

export const changesQuery = gql`
query Changes($pagination: PaginationInput) {
    changes(pagination: $pagination) {
        items {
            id
            type
            original {
                id
                name
            }
            suggestion {
                id
                name
            }
        }
        pageSize
        pageIndex
        length
    }
}`;

export const changeQuery = gql`
query Change($id: ChangeID!) {
    change(id: $id) {
        id
        type
        original {
            id
        }
        suggestion {
            id
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

export const acceptChange = gql`
mutation AcceptChange($id: ChangeID!) {
    acceptChange(id: $id) {
        id
    }
}`;
export const rejectChange = gql`
mutation RejectChange($id: ChangeID!) {
    rejectChange(id: $id)
}`;

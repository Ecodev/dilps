import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

export const cardDetailsFragment = gql`
fragment cardDetails on Card {
    id
    name
    expandedName
    hasImage
    height
    width
    visibility
    dating
    artists {
        id
        name
    }
    institution {
        id
        name
        locality
        street
        postcode
        latitude
        longitude
        country {
            id
            code
            name
        }
    }
    status
    original {
        width
        height
    }
    addition
    material
    technique
    techniqueAuthor
    format
    literature
    page
    figure
    table
    isbn
    comment
    rights
    muserisUrl
    muserisCote
    locality
    street
    postcode
    latitude
    longitude
    country {
        id
        code
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


export const cardsQuery = gql`
query Cards($filters: CardFilter, $pagination: PaginationInput) {
    cards(filters: $filters, pagination: $pagination) {
        items {
            id
            name
            width
            height
            hasImage
        }
        pageSize
        pageIndex
        length
    }
}`;

export const cardQuery = gql`
query Card($id: CardID!) {
    card(id: $id) {
        ...cardDetails
    }
}${cardDetailsFragment}`;

export const createCardMutation = gql`
mutation CreateCard ($input: CardInput!) {
    createCard (input: $input) {
        id
        creationDate
        creator {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const updateCardMutation = gql`
mutation UpdateCard($id: CardID!, $input: CardInput!) {
    updateCard(id: $id, input: $input) {
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const deleteCardsMutation = gql`
mutation DeleteCards ($ids: [CardID!]!){
    deleteCards(ids: $ids)
}`;

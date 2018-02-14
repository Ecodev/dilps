import gql from 'graphql-tag';
import { userMetaFragment } from '../../shared/queries/fragments';

export const artistsQuery = gql`
query Artists($filters: ArtistFilter, $pagination: PaginationInput) {
    artists(filters: $filters, pagination: $pagination) {
        items {
            id
            name
        }
        pageSize
        pageIndex
        length
    }
}`;

export const artistQuery = gql`
query Artist($id: ArtistID!) {
    artist(id: $id) {
        id
        name
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

export const createArtistMutation = gql`
mutation CreateArtist ($input: ArtistInput!) {
    createArtist (input: $input) {
        id
        creationDate
        creator {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const updateArtistMutation = gql`
mutation UpdateArtist($id: ArtistID!, $input: ArtistInput!) {
    updateArtist(id: $id, input: $input) {
        updateDate
        updater {
            ...userMeta
        }
    }
}${userMetaFragment}`;

export const deleteArtistMutation = gql`
mutation DeleteArtist ($id: ArtistID!){
    deleteArtist(id: $id)
}`;

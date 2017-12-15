import gql from 'graphql-tag';

export const imagesQuery = gql`
query Images($pagination: PaginationInput) {
    images(pagination: $pagination) {
        items {
            id
            name
            width
            height
        }
        pageSize
        pageIndex
        length
    }
}`;

export const imageQuery = gql`
query Image($id: ImageID!) {
    image(id: $id) {
        id
        name
        expandedName
        isPublic
        dating
        artists {
            id
            name
        }
        institution {
            id
            name
        }
        type
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

export const createImageMutation = gql`
mutation CreateImage ($input: ImageInput!) {
    createImage (input: $input) {
        id
    }
}`;

export const updateImageMutation = gql`
mutation UpdateImage($id: ImageID!, $input: ImageInput!) {
    updateImage(id: $id, input: $input) {
        id
    }
}`;

export const deleteImageMutation = gql`
mutation DeleteImage ($id: ImageID!){
    deleteImage(id: $id)
}`;

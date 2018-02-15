<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            linkCollectionImage(collection: 2000, image: 6001) {
                id
                images {
                    id
                }
            }
    
            linkImageArtist(image: 6001, artist: "Brand new artist name") {
                id
                artists {
                    name
                }
            }
    
            linkImageTag(image: 6001, tag: "Brand new tag name") {
                id
                tags {
                    name
                }
            }
        }',
    ],
    [
        'data' => [
            'linkCollectionImage' => [
                'id' => '2000',
                'images' => [
                    [
                        'id' => '6000',
                    ],
                    [
                        'id' => '6001',
                    ],
                ],
            ],
            'linkImageArtist' => [
                'id' => '6001',
                'artists' => [
                    [
                        'name' => 'Brand new artist name',
                    ],
                ],
            ],
            'linkImageTag' => [
                'id' => '6001',
                'tags' => [
                    [
                        'name' => 'Brand new tag name',
                    ],
                ],
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            unlinkCollectionImage(collection: 2000, image: 6000) {
                id
                images {
                    id
                }
            }
    
            unlinkImageArtist(image: 6000, artist: "Test artist 3000") {
                id
                artists {
                    name
                }
            }
    
            unlinkImageTag(image: 6000, tag: "Test tag 4000") {
                id
                tags {
                    name
                }
            }
        }',
    ],
    [
        'data' => [
            'unlinkCollectionImage' => [
                'id' => '2000',
                'images' => [],
            ],
            'unlinkImageArtist' => [
                'id' => '6000',
                'artists' => [],
            ],
            'unlinkImageTag' => [
                'id' => '6000',
                'tags' => [],
            ],
        ],
    ],
];

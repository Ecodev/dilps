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
        }',
    ],
    [
        'data' => [
            'unlinkCollectionImage' => [
                'id' => '2000',
                'images' => [],
            ],
        ],
    ],
];

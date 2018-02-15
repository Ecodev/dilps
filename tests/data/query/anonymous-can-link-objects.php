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
        ],
    ],
];

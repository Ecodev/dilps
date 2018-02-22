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

            linkImageImage(image1: 6000, image2: 6001) {
                id
                images {
                    id
                    images {
                        id
                    }
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
            'linkImageImage' => [
                'id' => '6000',
                'images' => [
                    [
                        'id' => '6001',
                        'images' => [
                            [
                                'id' => '6000',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

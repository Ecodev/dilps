<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            linkCollectionCard(collection: 2000, card: 6001) {
                id
                cards {
                    id
                }
            }

            linkCardCard(card1: 6000, card2: 6001) {
                id
                cards {
                    id
                    cards {
                        id
                    }
                }
            }
        }',
    ],
    [
        'data' => [
            'linkCollectionCard' => [
                'id' => '2000',
                'cards' => [
                    [
                        'id' => '6000',
                    ],
                    [
                        'id' => '6001',
                    ],
                ],
            ],
            'linkCardCard' => [
                'id' => '6000',
                'cards' => [
                    [
                        'id' => '6005',
                        'cards' => [
                            [
                                'id' => '6000',
                            ],
                        ],
                    ],
                    [
                        'id' => '6001',
                        'cards' => [
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

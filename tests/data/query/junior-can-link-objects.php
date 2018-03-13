<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            linkCollectionCard(collection: 2002, card: 6005) {
                id
                cards {
                    id
                }
            }

            linkCardCard(card1: 6006, card2: 6005) {
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
                'id' => '2002',
                'cards' => [
                    [
                        'id' => '6005',
                    ],
                ],
            ],
            'linkCardCard' => [
                'id' => '6006',
                'cards' => [
                    [
                        'id' => '6005',
                        'cards' => [
                            [
                                'id' => '6006',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

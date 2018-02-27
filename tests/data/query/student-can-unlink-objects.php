<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            unlinkCollectionCard(collection: 2000, card: 6000) {
                id
                cards {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'unlinkCollectionCard' => [
                'id' => '2000',
                'cards' => [],
            ],
        ],
    ],
];

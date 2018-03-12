<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            cards(sort: "card.id") {
                items {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'cards' => [
                'items' => [
                    [
                        'id' => 6000,
                    ],
                    [
                        'id' => 6001,
                    ],
                    [
                        'id' => 6002,
                    ],
                    [
                        'id' => 6003,
                    ],
                    [
                        'id' => 6004,
                    ],
                    [
                        'id' => 6005,
                    ],
                ],
            ],
        ],
    ],
];

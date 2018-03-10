<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            cards(sort: "random") {
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
                        'id' => 6003,
                    ],
                    [
                        'id' => 6000,
                    ],
                    [
                        'id' => 6004,
                    ],
                    [
                        'id' => 6001,
                    ],
                    [
                        'id' => 6005,
                    ],
                    [
                        'id' => 6002,
                    ],
                ],
            ],
        ],
    ],
];

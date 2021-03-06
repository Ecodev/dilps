<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            cards(sorting: [{field: id, order: ASC}]) {
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
                    [
                        'id' => 6006,
                    ],
                ],
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            card(id: 6005) {
                id
                original {
                    id
                    width
                }
            }
        }',
    ],
    [
        'data' => [
            'card' => [
                'id' => 6005,
                'original' => null, // Null because we don't have access on it
            ],
        ],
    ],
];

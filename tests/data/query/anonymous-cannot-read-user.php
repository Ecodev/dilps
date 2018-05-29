<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            user(id: "1000") {
                id
            }
        }',
    ],
    [
        'data' => [
            'user' => null,
        ],
        'errors' => [
            [
                'message' => 'Entity not found for class `Application\Model\User` and ID `1000`.',
                'category' => 'graphql',
                'locations' => [
                    [
                        'line' => 2,
                        'column' => 13,
                    ],
                ],
                'path' => [
                    'user',
                ],

            ],
        ],
    ],
];

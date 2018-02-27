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
                'message' => 'Non-logged user with role anonymous is not allowed on resource "User#1000" with privilege "read"',
                'category' => 'Permissions',
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

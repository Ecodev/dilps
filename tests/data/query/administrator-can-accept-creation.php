<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7000) {
                name
                owner {
                    id
                }
                creator {
                    id
                }
                updater {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'acceptChange' => [
                'name' => 'Test suggestion card 6001',
                'owner' => [
                    'id' => '1000',
                ],
                'creator' => [
                    'id' => '1003',
                ],
                'updater' => [
                    'id' => '1000',
                ],
            ],
        ],
    ],
];

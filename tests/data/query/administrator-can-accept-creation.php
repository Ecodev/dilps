<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7000) {
                name
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

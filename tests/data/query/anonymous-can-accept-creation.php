<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7000) {
                name
            }
        }',
    ],
    [
        'data' => [
            'acceptChange' => [
                'name' => 'Test suggestion card 6001',
            ],
        ],
    ],
];

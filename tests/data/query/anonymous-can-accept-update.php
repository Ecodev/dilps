<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7001) {
                id
                name
            }
        }',
    ],
    [
        'data' => [
            'acceptChange' => [
                'id' => '6000',
                'name' => 'Test suggestion image 6002',
            ],
        ],
    ],
];

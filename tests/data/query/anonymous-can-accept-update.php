<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7001, response: "test response") {
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

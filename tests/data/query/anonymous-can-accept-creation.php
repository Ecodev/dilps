<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7000, response: "test response") {
                name
            }
        }',
    ],
    [
        'data' => [
            'acceptChange' => [
                'name' => 'Test suggestion image 6001',
            ],
        ],
    ],
];

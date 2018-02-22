<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7002, response: "test response") {
                id
                name
            }
        }',
    ],
    [
        'data' => [
            'acceptChange' => null,
        ],
    ],
];

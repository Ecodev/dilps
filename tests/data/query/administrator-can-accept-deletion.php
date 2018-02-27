<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            acceptChange(id: 7002) {
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

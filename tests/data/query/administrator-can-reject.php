<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            r1: rejectChange(id: 7000)

            r2: rejectChange(id: 7001)

            r3: rejectChange(id: 7002)
        }',
    ],
    [
        'data' => [
            'r1' => true,
            'r2' => true,
            'r3' => true,
        ],
    ],
];

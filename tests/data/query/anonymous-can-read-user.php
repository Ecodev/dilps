<?php

declare(strict_types=1);

return [
    '{
        user(id: "10000") {
            id
        }
    }',
    [],
    [
        'data' => [
            'user' => [
                'id' => '10000',
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

return [
    '{
        user(id: "1000") {
            id
        }
    }',
    [],
    [
        'data' => [
            'user' => [
                'id' => '1000',
            ],
        ],
    ],
];

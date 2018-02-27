<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            user(id: "1000") {
                id
            }
        }',
    ],
    [
        'data' => [
            'user' => [
                'id' => '1000',
            ],
        ],
    ],
];

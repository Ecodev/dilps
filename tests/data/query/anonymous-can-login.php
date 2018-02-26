<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            login(login: "Alice" password: "money") {
                id
                login
            }
        }',
    ],
    [
        'data' => [
            'login' => [
                'id' => '1000',
                'login' => 'Alice',
            ],
        ],
    ],
];

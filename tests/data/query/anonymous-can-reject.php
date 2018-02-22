<?php

declare(strict_types=1);

use Application\Model\Change;

return [
    [
        'query' => 'mutation {
            a1: rejectChange(id: 7000, response: "test response") {
                id
                status
            }
            
            a2: rejectChange(id: 7001, response: "test response") {
                id
                status
            }
            
            a3: rejectChange(id: 7002, response: "test response") {
                id
                status
            }
        }',
    ],
    [
        'data' => [
            'a1' => [
                'id' => '7000',
                'status' => Change::STATUS_REJECTED,
            ],
            'a2' => [
                'id' => '7001',
                'status' => Change::STATUS_REJECTED,
            ],
            'a3' => [
                'id' => '7002',
                'status' => Change::STATUS_REJECTED,
            ],
        ],
    ],
];

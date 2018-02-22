<?php

declare(strict_types=1);

use Application\Model\Change;

return [
    [
        'query' => 'mutation {
            suggestCreation(id: 6003, request: "test request") {
                type
                request
                original {
                    id
                }
                suggestion {
                    id
                }
            }

            suggestUpdate(id: 6004, request: "test request") {
                type
                request
                original {
                    id
                }
                suggestion {
                    id
                }
            }

            suggestDeletion(id: 6004, request: "test request") {
                type
                request
                original {
                    id
                }
                suggestion {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'suggestCreation' => [
                'type' => Change::TYPE_CREATE,
                'request' => 'test request',
                'original' => null,
                'suggestion' => [
                    'id' => '6003',
                ],
            ],
            'suggestUpdate' => [
                'type' => Change::TYPE_UPDATE,
                'request' => 'test request',
                'original' => [
                    'id' => '6000',
                ],
                'suggestion' => [
                    'id' => '6004',
                ],
            ],
            'suggestDeletion' => [
                'type' => Change::TYPE_DELETE,
                'request' => 'test request',
                'original' => [
                    'id' => '6004',
                ],
                'suggestion' => null,
            ],
        ],
    ],
];

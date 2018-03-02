<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            validateData(id: 6000) {
                dataValidator {
                    id
                }
                imageValidator {
                    id
                }
            }

            validateImage(id: 6001) {
                dataValidator {
                    id
                }
                imageValidator {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'validateData' => [
                'dataValidator' => [
                    'id' => '1000',
                ],
                'imageValidator' => null,
            ],
            'validateImage' => [
                'dataValidator' => null,
                'imageValidator' => [
                    'id' => '1000',
                ],
            ],
        ],
    ],
];

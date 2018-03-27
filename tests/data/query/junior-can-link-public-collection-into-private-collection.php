<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            linkCollectionToCollection(sourceCollection: 2001, targetCollection: 2002) {
                id
            }
        }',
    ],
    [
        'data' => [
            'linkCollectionToCollection' => [
                'id' => '2002',
            ],
        ],
    ],
];

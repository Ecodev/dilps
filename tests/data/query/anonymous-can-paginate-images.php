<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            images(pagination: {pageIndex:1, pageSize: 2}) {
                length
                pageIndex
                items {
                    id
                }
            }
        }',
    ],
    [
        'data' => [
            'images' => [
                'length' => 3,
                'pageIndex' => 1,
                'items' => [
                    [
                        'id' => 6002,
                    ],
                ],
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            cards(pagination: {pageIndex:1, pageSize: 2}) {
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
            'cards' => [
                'length' => 7,
                'pageIndex' => 1,
                'items' => [
                    [
                        'id' => 6002,
                    ],
                    [
                        'id' => 6003,
                    ],
                ],
            ],
        ],
    ],
];

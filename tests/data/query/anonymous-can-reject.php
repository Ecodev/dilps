<?php

declare(strict_types=1);

return [
    [
        'query' => 'mutation {
            a1: rejectChange(id: 7000) 
            
            a2: rejectChange(id: 7001) 
            
            a3: rejectChange(id: 7002) 
        }',
    ],
    [
        'data' => [
            'a1' => true,
            'a2' => true,
            'a3' => true,
        ],
    ],
];

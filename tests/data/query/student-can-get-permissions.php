<?php

declare(strict_types=1);

return [
    [
        'query' => '{
            viewer {
                globalPermissions {
                artist { create }
                card { create }
                change { create }
                collection { create }
                country { create }
                dating { create }
                institution { create }
                tag { create }
                user { create }
                }
            }

            card (id: 6000) {
                permissions {
                    create
                    read
                    update
                    delete
                }
            }
        }',
    ],
    [
        'data' => [
            'viewer' => [
                'globalPermissions' => [
                    'artist' => [
                        'create' => true,
                    ],
                    'card' => [
                        'create' => true,
                    ],
                    'change' => [
                        'create' => true,
                    ],
                    'collection' => [
                        'create' => true,
                    ],
                    'country' => [
                        'create' => false,
                    ],
                    'dating' => [
                        'create' => false,
                    ],
                    'institution' => [
                        'create' => true,
                    ],
                    'tag' => [
                        'create' => true,
                    ],
                    'user' => [
                        'create' => false,
                    ],
                ],
            ],
            'card' => [
                'permissions' => [
                    'create' => true,
                    'read' => true,
                    'update' => true,
                    'delete' => true,
                ],
            ],
        ],
    ],
];

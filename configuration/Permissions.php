<?php

declare(strict_types=1);

return [
    [
        'path' => 'logs',
        'permissions' => '0770',
        'recursive' => true,
    ],
    [
        'path' => 'data/cache',
        'permissions' => '0770',
        'recursive' => true,
    ],
    [
        'path' => 'bin/build.sh',
        'permissions' => '0770',
    ],
    [
        'path' => 'node_modules/.bin/*',
        'permissions' => '0770',
    ],
    [
        'path' => 'bin/pre-commit.sh',
        'permissions' => '0770',
    ],
];

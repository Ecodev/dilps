<?php

declare(strict_types=1);

namespace Application\Api\Output;

use GraphQL\Type\Definition\ObjectType;

class GlobalPermissionsType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'GlobalPermissions',
            'description' => 'Describe global permissions for current user',
            'fields' => [
                'create' => [
                    'type' => self::nonNull(self::boolean()),
                    'description' => 'Whether the current logged in user can create',
                ],
            ],
        ];

        parent::__construct($config);
    }
}

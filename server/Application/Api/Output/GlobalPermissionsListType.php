<?php

declare(strict_types=1);

namespace Application\Api\Output;

use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

class GlobalPermissionsListType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'GlobalPermissionsList',
            'description' => 'Describe permissions for current user',
            'fields' => function (): array {
                $user = new User();
                $globalPermissions = $user->getGlobalPermissions();

                $fields = [];
                foreach ($globalPermissions as $class => $perm) {
                    $fields[$class] = [
                        'type' => _types()->get(GlobalPermissionsType::class),
                    ];
                }

                return $fields;
            },
        ];

        parent::__construct($config);
    }
}

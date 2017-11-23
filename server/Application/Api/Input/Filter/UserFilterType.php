<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use GraphQL\Type\Definition\InputObjectType;

class UserFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'firstname' => [
                        'type' => self::string(),
                    ],
                    'lastname' => [
                        'type' => self::string(),
                    ],
                    'email' => [
                        'type' => self::string(),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

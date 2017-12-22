<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use GraphQL\Type\Definition\InputObjectType;

class InstitutionFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'search' => [
                        'type' => self::string(),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

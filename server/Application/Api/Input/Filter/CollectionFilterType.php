<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use GraphQL\Type\Definition\InputObjectType;

class CollectionFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'creators' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                    'isSource' => [
                        'type' => self::boolean(),
                    ],
                    'search' => [
                        'type' => self::string(),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

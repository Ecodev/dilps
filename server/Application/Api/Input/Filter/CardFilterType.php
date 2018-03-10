<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use GraphQL\Type\Definition\InputObjectType;

class CardFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'search' => [
                        'type' => self::string(),
                    ],
                    'ids' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                    'collections' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                    'creators' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

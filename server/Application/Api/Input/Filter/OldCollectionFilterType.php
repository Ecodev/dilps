<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use Application\Api\Enum\CollectionVisibilityType;
use GraphQL\Type\Definition\InputObjectType;

class OldCollectionFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'creators' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                    'parents' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                    'isSource' => [
                        'type' => self::boolean(),
                    ],
                    'search' => [
                        'type' => self::string(),
                    ],
                    'visibilities' => [
                        'type' => self::listOf(self::nonNull(_types()->get(CollectionVisibilityType::class))),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

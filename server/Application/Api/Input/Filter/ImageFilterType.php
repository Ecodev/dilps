<?php

declare(strict_types=1);

namespace Application\Api\Input\Filter;

use GraphQL\Type\Definition\InputObjectType;

class ImageFilterType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function (): array {
                return [
                    'search' => [
                        'type' => self::string(),
                    ],
                    'collections' => [
                        'type' => self::listOf(self::nonNull(self::id())),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

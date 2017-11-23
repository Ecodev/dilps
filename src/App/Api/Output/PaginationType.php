<?php

declare(strict_types=1);

namespace App\Api\Output;

use GraphQL\Type\Definition\ObjectType;

class PaginationType extends ObjectType
{
    public static function build(string $class)
    {
        return new self($class);
    }

    public function __construct(string $class)
    {
        $c = new \ReflectionClass($class);
        $s = $c->getShortName();
        $name = $s . 'Pagination';

        $config = [
            'name' => $name,
            'description' => 'Describe available pages',
            'fields' => function () use ($class): array {
                return [
                    'pageIndex' => [
                        'type' => self::nonNull(self::int()),
                        'description' => 'The zero-based page index of the displayed list of items',
                    ],
                    'pageSize' => [
                        'type' => self::nonNull(self::int()),
                        'description' => 'Number of items to display on a page',
                    ],
                    'length' => [
                        'type' => self::nonNull(self::int()),
                        'description' => 'The length of the total number of items that are being paginated',
                    ],
                    'items' => [
                        'type' => self::nonNull(self::listOf(_types()->get($class))),
                        'description' => 'Paginated items',
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

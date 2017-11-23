<?php

declare(strict_types=1);

namespace Application\Api;

/**
 * Our API schema
 */
class Schema extends \GraphQL\Type\Schema
{
    public function __construct()
    {
        $config = [
            'query' => _types()->get(QueryType::class),
            'mutation' => _types()->get(MutationType::class),
        ];

        parent::__construct($config);
    }
}

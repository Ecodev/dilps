<?php

namespace App\Api;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

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

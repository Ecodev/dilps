<?php

namespace App\Api;

use GraphQL\Type\Definition\ObjectType;
use App\Api\Field\Standard;
use App\Model\User;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
            ],
        ];

        $config['fields'] += Standard::buildQuery(User::class);

        parent::__construct($config);
    }
}

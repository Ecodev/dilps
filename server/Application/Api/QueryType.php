<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Standard;
use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

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
<?php

namespace App\Api;

use GraphQL\Type\Definition\ObjectType;
use App\Api\Field\Standard;
use App\Model\User;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
            ],
        ];

        $config['fields'] += Standard::buildMutation(User::class);

        parent::__construct($config);
    }
}

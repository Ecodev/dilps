<?php

declare(strict_types=1);

namespace App\Api;

use App\Api\Field\Standard;
use App\Model\User;
use GraphQL\Type\Definition\ObjectType;

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

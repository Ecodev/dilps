<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Standard;
use Application\Model\Artist;
use Application\Model\Change;
use Application\Model\Collection;
use Application\Model\Image;
use Application\Model\Institution;
use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [],
        ];

        $config['fields'] += Standard::buildQuery(Artist::class);
        $config['fields'] += Standard::buildQuery(Change::class);
        $config['fields'] += Standard::buildQuery(Collection::class);
        $config['fields'] += Standard::buildQuery(Image::class);
        $config['fields'] += Standard::buildQuery(Institution::class);
        $config['fields'] += Standard::buildQuery(User::class);

        parent::__construct($config);
    }
}

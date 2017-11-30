<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Standard;
use Application\Model\Artist;
use Application\Model\Collection;
use Application\Model\Image;
use Application\Model\Institution;
use Application\Model\Tag;
use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => [
            ],
        ];

        $config['fields'] += Standard::buildMutation(Artist::class);
        $config['fields'] += Standard::buildMutation(Collection::class);
        $config['fields'] += Standard::buildMutation(Institution::class);
        $config['fields'] += Standard::buildMutation(Tag::class);
        $config['fields'] += Standard::buildMutation(Image::class);
        $config['fields'] += Standard::buildMutation(User::class);

        parent::__construct($config);
    }
}

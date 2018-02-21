<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Standard;
use Application\Model\Artist;
use Application\Model\Collection;
use Application\Model\Image;
use Application\Model\Institution;
use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $specializedFields = [
        ];

        $fields = array_merge(
            $specializedFields,

            Standard::buildMutation(Artist::class),
            Standard::buildMutation(Collection::class),
            Standard::buildMutation(Institution::class),
            Standard::buildMutation(Image::class),
            Standard::buildMutation(User::class),
            Standard::buildRelationMutation(Collection::class, Image::class)
        );

        $config = [
            'fields' => $fields,
        ];

        parent::__construct($config);
    }
}

<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Mutation\AcceptChange;
use Application\Api\Field\Mutation\SuggestCreation;
use Application\Api\Field\Mutation\SuggestDeletion;
use Application\Api\Field\Mutation\SuggestUpdate;
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
            SuggestCreation::build(),
            SuggestUpdate::build(),
            SuggestDeletion::build(),
            AcceptChange::build(),
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

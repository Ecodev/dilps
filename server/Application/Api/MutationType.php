<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Mutation\AcceptChange;
use Application\Api\Field\Mutation\Login;
use Application\Api\Field\Mutation\Logout;
use Application\Api\Field\Mutation\RejectChange;
use Application\Api\Field\Mutation\SuggestCreation;
use Application\Api\Field\Mutation\SuggestDeletion;
use Application\Api\Field\Mutation\SuggestUpdate;
use Application\Api\Field\Mutation\ValidateData;
use Application\Api\Field\Mutation\ValidateImage;
use Application\Api\Field\Standard;
use Application\Model\Artist;
use Application\Model\Card;
use Application\Model\Collection;
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
            RejectChange::build(),
            Login::build(),
            Logout::build(),
            ValidateData::build(),
            ValidateImage::build(),
        ];

        $fields = array_merge(
            $specializedFields,

            Standard::buildMutation(Artist::class),
            Standard::buildMutation(Collection::class),
            Standard::buildMutation(Institution::class),
            Standard::buildMutation(Card::class),
            Standard::buildMutation(User::class),
            Standard::buildRelationMutation(Collection::class, Card::class),
            Standard::buildRelationMutation(Card::class, Card::class)
        );

        $config = [
            'fields' => $fields,
        ];

        parent::__construct($config);
    }
}

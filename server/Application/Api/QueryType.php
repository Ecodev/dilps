<?php

declare(strict_types=1);

namespace Application\Api;

use Application\Api\Field\Standard;
use Application\Model\Artist;
use Application\Model\Card;
use Application\Model\Change;
use Application\Model\Collection;
use Application\Model\Country;
use Application\Model\Institution;
use Application\Model\User;
use GraphQL\Type\Definition\ObjectType;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $specializedFields = [
        ];

        $fields = array_merge(
            $specializedFields,

            Standard::buildQuery(Artist::class),
            Standard::buildQuery(Change::class),
            Standard::buildQuery(Collection::class),
            Standard::buildQuery(Card::class),
            Standard::buildQuery(Institution::class),
            Standard::buildQuery(User::class),
            Standard::buildQuery(Country::class)
        );

        $config = [
            'fields' => $fields,
        ];

        parent::__construct($config);
    }
}

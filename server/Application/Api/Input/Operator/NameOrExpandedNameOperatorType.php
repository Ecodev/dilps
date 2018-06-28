<?php

declare(strict_types=1);

namespace Application\Api\Input\Operator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Factory\UniqueNameFactory;

class NameOrExpandedNameOperatorType extends SearchOperatorType
{
    protected function getSearchableFields(UniqueNameFactory $uniqueNameFactory, ClassMetadata $metadata, QueryBuilder $queryBuilder, string $alias): array
    {
        return [
            $alias . '.name',
            $alias . '.expandedName',
        ];
    }
}

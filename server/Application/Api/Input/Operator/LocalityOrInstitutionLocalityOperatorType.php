<?php

declare(strict_types=1);

namespace Application\Api\Input\Operator;

use Application\Model\Institution;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Factory\UniqueNameFactory;

class LocalityOrInstitutionLocalityOperatorType extends SearchOperatorType
{
    protected function getSearchableFields(UniqueNameFactory $uniqueNameFactory, ClassMetadata $metadata, QueryBuilder $queryBuilder, string $alias): array
    {
        $institution = $uniqueNameFactory->createAliasName(Institution::class);
        $queryBuilder->leftJoin($alias . '.institution', $institution);

        return [
            $institution . '.locality',
            $alias . '.locality',
        ];
    }
}

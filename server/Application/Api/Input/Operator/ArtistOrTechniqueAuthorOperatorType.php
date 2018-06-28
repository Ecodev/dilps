<?php

declare(strict_types=1);

namespace Application\Api\Input\Operator;

use Application\Model\Artist;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Factory\UniqueNameFactory;

class ArtistOrTechniqueAuthorOperatorType extends SearchOperatorType
{
    protected function getSearchableFields(UniqueNameFactory $uniqueNameFactory, ClassMetadata $metadata, QueryBuilder $queryBuilder, string $alias): array
    {
        $artist = $uniqueNameFactory->createAliasName(Artist::class);
        $queryBuilder->leftJoin($alias . '.artists', $artist);

        return [
            $artist . '.name',
            $alias . '.techniqueAuthor',
        ];
    }
}

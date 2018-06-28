<?php

declare(strict_types=1);

namespace Application\Api\Input\Sorting;

use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Sorting\SortingInterface;

class Artists implements SortingInterface
{
    public function __construct()
    {
    }

    public function __invoke(QueryBuilder $queryBuilder, string $order): void
    {
        $alias = $queryBuilder->getDQLPart('from')[0]->getAlias();
        $queryBuilder->leftJoin($alias . '.artists', 'sortingArtist');
        $queryBuilder->addOrderBy('sortingArtist.name', $order);
    }
}

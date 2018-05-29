<?php

declare(strict_types=1);

namespace Application\Api\Input\Sorting;

use Doctrine\ORM\QueryBuilder;
use GraphQL\Doctrine\Sorting\SortingInterface;

class Random implements SortingInterface
{
    public function __construct()
    {
    }

    public function __invoke(QueryBuilder $queryBuilder, string $order): void
    {
        $queryBuilder->addOrderBy('RAND()');
    }
}

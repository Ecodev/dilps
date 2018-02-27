<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class CardRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('card');

        if ($filters['collections'] ?? null) {
            $qb->join('card.collections', 'collection');
            $qb->andWhere('collection.id IN (:collections)');
            $qb->setParameter('collections', $filters['collections']);
        }

        if (@$filters['search']) {
            $qb->andWhere('card.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        $qb->addOrderBy('card.id');

        return $qb;
    }
}

<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class ImageRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('image');

        if ($filters['collections'] ?? null) {
            $qb->join('image.collections', 'collection');
            $qb->andWhere('collection.id IN (:collections)');
            $qb->setParameter('collections', $filters['collections']);
        }

        if (@$filters['search']) {
            $qb->andWhere('image.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb;
    }
}

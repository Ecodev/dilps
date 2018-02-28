<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class CollectionRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('collection');

        if (isset($filters['isSource'])) {
            $qb->andWhere('collection.isSource = :isSource');
            $qb->setParameter('isSource', $filters['isSource']);
        }

        if (isset($filters['creators'])) {
            if (\count($filters['creators']) > 0) {
                $qb->andWhere('collection.creator IN (:creators)');
                $qb->setParameter('creators', $filters['creators']);
            } else {
                $qb->andWhere('collection.creator IS NULL');
            }
        }

        $qb->addOrderBy('collection.id');

        return $qb;
    }
}

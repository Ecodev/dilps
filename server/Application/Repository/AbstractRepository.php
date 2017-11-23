<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends EntityRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        foreach ($filters as $key => $value) {
            $qb->andWhere('o.' . $key . '=' . $this->getEntityManager()->getConnection()->quote($value));
        }

        return $qb;
    }
}

<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class InstitutionRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('institution');

        if (@$filters['search']) {
            $qb->andWhere('institution.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb;
    }
}

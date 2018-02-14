<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class ArtistRepository extends AbstractRepository
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('artist');

        if (@$filters['search']) {
            $qb->andWhere('artist.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb;
    }
}

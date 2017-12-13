<?php

declare(strict_types=1);

namespace Application\Repository;

use Doctrine\ORM\QueryBuilder;

class UserRepository extends AbstractRepository
{

    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('user');

        if (@$filters['login']) {
            $qb->andWhere('user.login LIKE :login');
            $qb->setParameter('login', '%' . $filters['login'] . '%');
        }

        if (@$filters['email']) {
            $qb->andWhere('user.email LIKE :email');
            $qb->setParameter('email', '%' . $filters['email'] . '%');
        }

        if (@$filters['search']) {
            $qb->andWhere('user.login LIKE :search OR user.email LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb;
    }

}

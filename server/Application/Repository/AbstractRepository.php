<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\ORM\Query\Filter\AclFilter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends EntityRepository
{
    public function getFindAllQuery(array $filters = [], string $sort = 'o.id'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        foreach ($filters as $key => $value) {
            $qb->andWhere('o.' . $key . '=' . $this->getEntityManager()->getConnection()->quote($value));
        }

        $qb->addOrderBy($sort);

        return $qb;
    }

    /**
     * Returns the AclFilter to fetch ACL filtering SQL
     *
     * @return AclFilter
     */
    protected function getAclFilter(): AclFilter
    {
        return $this->getEntityManager()->getFilters()->getFilter(AclFilter::class);
    }

    /**
     * Return all ID
     *
     * @return string
     */
    protected function getAllIdsQuery(): string
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id')
            ->from($this->getClassMetadata()->getTableName());

        return $qb->getSQL();
    }

    protected function quoteArray(array $values): string
    {
        $result = [];
        foreach ($values as $v) {
            $result[] = $this->getEntityManager()->getConnection()->quote($v);
        }

        return implode(', ', $result);
    }
}

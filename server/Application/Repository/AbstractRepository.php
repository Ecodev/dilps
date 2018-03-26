<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\ORM\Query\Filter\AclFilter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends EntityRepository
{
    public function getFindAllQuery(array $filters = [], string $sort = 'o.id', string $order = 'ASC'): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        foreach ($filters as $key => $value) {
            $qb->andWhere('o.' . $key . '=' . $this->getEntityManager()->getConnection()->quote($value));
        }

        $qb->addOrderBy($sort, $order);

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

    /**
     * Modify $qb to add a constraint to search for all words contained in $search.
     *
     * @param QueryBuilder $qb
     * @param string $search
     * @param array $fields an array of table.field strings
     */
    protected function addSearch(QueryBuilder $qb, string $search, array $fields): void
    {
        if (!trim($search)) {
            return;
        }

        // Build the WHERE clause
        $wordWheres = [];
        foreach (preg_split('/[[:space:]]+/', $search, -1, PREG_SPLIT_NO_EMPTY) as $i => $word) {
            $parameterName = 'searchWord' . $i;

            $fieldWheres = [];
            foreach ($fields as $field) {
                $fieldWheres[] = $field . ' LIKE :' . $parameterName;
            }

            if ($fieldWheres) {
                $wordWheres[] = '(' . implode(' OR ', $fieldWheres) . ')';
                $qb->setParameter($parameterName, '%' . $word . '%');
            }
        }

        if ($wordWheres) {
            $qb->andWhere('(' . implode(' AND ', $wordWheres) . ')');
        }
    }
}

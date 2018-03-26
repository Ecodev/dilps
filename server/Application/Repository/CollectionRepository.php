<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Collection;
use Application\Model\User;
use Doctrine\ORM\QueryBuilder;

class CollectionRepository extends AbstractRepository implements LimitedAccessSubQueryInterface
{
    public function getFindAllQuery(array $filters = [], string $sort = 'collection.name', string $order = 'ASC'): QueryBuilder
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

        if (isset($filters['parents'])) {
            if (\count($filters['parents']) > 0) {
                $qb->andWhere('collection.parent IN (:parents)');
                $qb->setParameter('parents', $filters['parents']);
            } else {
                $qb->andWhere('collection.parent IS NULL');
            }
        }

        if (isset($filters['visibilities'])) {
            if (\count($filters['visibilities']) > 0) {
                $qb->andWhere('collection.visibility IN (:visibilities)');
                $qb->setParameter('visibilities', $filters['visibilities']);
            } else {
                $qb->andWhere('collection.visibility IS NULL');
            }
        }

        $this->addSearch($qb, @$filters['search'], ['collection.name']);

        $qb->addOrderBy($sort, $order);

        return $qb;
    }

    /**
     * Returns pure SQL to get ID of all collections that are accessible to given user.
     *
     * A collection is accessible if:
     *
     * - collection is member and user is logged in
     * - collection is admin and user is admin
     * - collection owner is the user
     *
     * @param null|User $user
     *
     * @return string
     */
    public function getAccessibleSubQuery(?User $user): string
    {
        if (!$user) {
            return '-1';
        }

        $visibility = [Collection::VISIBILITY_MEMBER];
        if ($user->getRole() === User::ROLE_ADMINISTRATOR) {
            $visibility[] = Collection::VISIBILITY_ADMINISTRATOR;
        }

        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('collection.id')
            ->from('collection')
            ->where('collection.visibility IN (' . $this->quoteArray($visibility) . ')');

        if ($user) {
            $qb->orWhere('collection.creator_id = ' . $this->getEntityManager()->getConnection()->quote($user->getId()));
        }

        return $qb->getSQL();
    }
}

<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Card;
use Application\Model\User;
use Doctrine\ORM\QueryBuilder;

class CollectionRepository extends AbstractRepository implements LimitedAccessSubQueryInterface
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

        if (isset($filters['visibilities'])) {
            if (\count($filters['visibilities']) > 0) {
                $qb->andWhere('collection.visibility IN (:visibilities)');
                $qb->setParameter('visibilities', $filters['visibilities']);
            } else {
                $qb->andWhere('collection.visibility IS NULL');
            }
        }

        if (@$filters['search']) {
            $qb->andWhere('collection.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        $qb->addOrderBy('collection.name');

        return $qb;
    }

    /**
     * Returns pure SQL to get ID of all collections that are accessible to given user.
     *
     * A collection is accessible if:
     *
     * - collection is public
     * - collection is member and user is logged in
     * - collection owner is the user
     *
     * @param null|User $user
     *
     * @return string
     */
    public function getAccessibleSubQuery(?User $user): string
    {
        $visibility = [Card::VISIBILITY_PUBLIC];
        if ($user) {
            $visibility[] = Card::VISIBILITY_MEMBER;
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

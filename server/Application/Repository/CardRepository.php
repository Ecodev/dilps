<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Card;
use Application\Model\User;
use Doctrine\ORM\QueryBuilder;

class CardRepository extends AbstractRepository implements LimitedAccessSubQueryInterface
{
    public function getFindAllQuery(array $filters = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('card');

        if ($filters['collections'] ?? null) {
            $qb->join('card.collections', 'collection');
            $qb->andWhere('collection.id IN (:collections)');
            $qb->setParameter('collections', $filters['collections']);
        }

        if (@$filters['search']) {
            $qb->andWhere('card.name LIKE :search');
            $qb->setParameter('search', '%' . $filters['search'] . '%');
        }

        $qb->addOrderBy('card.id');

        return $qb;
    }

    /**
     * Returns pure SQL to get ID of all cards that are accessible to given user.
     *
     * A card is accessible if:
     *
     * - card is public
     * - card is member and user is logged in
     * - card owner is the user
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
            ->select('card.id')
            ->from('card')
            ->where('card.visibility IN (' . $this->quoteArray($visibility) . ')');

        if ($user) {
            $qb->orWhere('card.creator_id = ' . $this->getEntityManager()->getConnection()->quote($user->getId()));
        }

        return $qb->getSQL();
    }
}

<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Card;
use Application\Model\User;
use Doctrine\ORM\QueryBuilder;

class CardRepository extends AbstractRepository implements LimitedAccessSubQueryInterface
{
    public function getFindAllQuery(array $filters = [], array $sorting = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('card');

        if (isset($filters['collections'])) {
            if (\count($filters['collections']) > 0) {
                $qb->join('card.collections', 'collection');
                $qb->andWhere('collection.id IN (:collections)');
                $qb->setParameter('collections', $filters['collections']);
            } else {
                $qb->andWhere('card.collections IS EMPTY');
            }
        }
        if (@$filters['ids'] ?? false) {
            $qb->andWhere('card.id IN (:ids)');
            $qb->setParameter('ids', $filters['ids']);
        }

        if (isset($filters['creators'])) {
            if (\count($filters['creators']) > 0) {
                $qb->andWhere('card.creator IN (:creators)');
                $qb->setParameter('creators', $filters['creators']);
            } else {
                $qb->andWhere('card.creator IS NULL');
            }
        }

        if (@$filters['search']) {
            $qb->leftJoin('card.institution', 'institution');
            $qb->leftJoin('card.artists', 'artist');
            $fields = [
                'card.name',
                'card.expandedName',
                'card.material',
                'card.technique',
                'card.addition',
                'card.locality',
                'institution.name',
                'artist.name',
            ];

            $this->addSearch($qb, $filters['search'] ?? '', $fields);
        }

        if (isset($filters['hasImage'])) {
            if ($filters['hasImage'] === true) {
                $qb->andWhere("card.filename != ''");
            } elseif ($filters['hasImage'] === false) {
                $qb->andWhere("card.filename = ''");
            }
        }

        $this->applySorting($qb, 'card', $sorting);

        return $qb;
    }

    /**
     * Returns pure SQL to get ID of all cards that are accessible to given user.
     * A card is accessible if:
     * - card is public
     * - card is member and user is logged in
     * - card owner or creator is the user
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

        $qb = $this->getEntityManager()
            ->getConnection()
            ->createQueryBuilder()
            ->select('card.id')
            ->from('card')
            ->where('card.visibility IN (' . $this->quoteArray($visibility) . ')');

        if ($user) {
            $userId = $this->getEntityManager()->getConnection()->quote($user->getId());
            $qb->orWhere('card.owner_id = ' . $userId . ' OR card.creator_id = ' . $userId);
        }

        return $qb->getSQL();
    }

    /**
     * Returns all unique filename in DB
     *
     * @return string[]
     */
    public function getFilenames(): array
    {
        $filenames = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->from('card')
            ->select('DISTINCT CONCAT("data/images/", filename)')
            ->where('filename != ""')
            ->orderBy('filename')->execute()->fetchAll(\PDO::FETCH_COLUMN);

        return $filenames;
    }
}

<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Card;
use Application\Model\Change;
use Application\Model\User;

class ChangeRepository extends AbstractRepository implements LimitedAccessSubQueryInterface
{
    /**
     * Get an open change for the given suggestion
     *
     * @param string $type
     * @param Card $card
     * @param string $request
     *
     * @return Change
     */
    public function getOrCreate(string $type, Card $card, string $request): Change
    {
        $criteria = [
            'type' => $type,
        ];

        if ($type === Change::TYPE_DELETE) {
            $criteria['original'] = $card;
            $original = $card;
            $suggestion = null;
        } else {
            $criteria['suggestion'] = $card;
            $original = $card->getOriginal();
            $suggestion = $card;
        }

        $change = $this->findOneBy($criteria);

        if (!$change) {

            // Create the change
            $change = new Change();
            _em()->persist($change);
            $change->setSuggestion($suggestion);
            $change->setOriginal($original);
            $change->setType($type);
            $change->setRequest($request);
        }

        return $change;
    }

    /**
     * Returns pure SQL to get ID of all changes that are accessible to given user.
     *
     * A change is accessible if:
     *
     * - change owner is the user
     *
     * @param null|User $user
     *
     * @return string
     */
    public function getAccessibleSubQuery(?User $user): string
    {
        if ($user) {
            $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->select('`change`.id')
                ->from('`change`')
                ->where('`change`.creator_id = ' . $this->getEntityManager()->getConnection()->quote($user->getId()));
        } else {
            return '-1';
        }

        return $qb->getSQL();
    }
}

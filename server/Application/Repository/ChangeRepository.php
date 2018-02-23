<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Card;
use Application\Model\Change;

class ChangeRepository extends AbstractRepository
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
}

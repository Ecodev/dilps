<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Model\Change;
use Application\Model\Image;

class ChangeRepository extends AbstractRepository
{
    /**
     * Get an open change for the given suggestion
     *
     * @param string $type
     * @param Image $image
     * @param string $request
     *
     * @return Change
     */
    public function getOrCreate(string $type, Image $image, string $request): Change
    {
        $criteria = [
            'type' => $type,
            'status' => Change::STATUS_NEW,
        ];

        if ($type === Change::TYPE_DELETE) {
            $criteria['original'] = $image;
            $original = $image;
            $suggestion = null;
        } else {
            $criteria['suggestion'] = $image;
            $original = $image->getOriginal();
            $suggestion = $image;
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

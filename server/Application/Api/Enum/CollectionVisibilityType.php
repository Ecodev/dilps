<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Collection;

class CollectionVisibilityType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Collection::VISIBILITY_PRIVATE => 'Visible only to owner',
            Collection::VISIBILITY_ADMINISTRATOR => 'Visible to administrators',
            Collection::VISIBILITY_MEMBER => 'Visible to any logged-in users',
        ];

        parent::__construct($config);
    }
}

<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Collection;

class CollectionVisibilityType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Collection::VISIBILITY_PRIVATE,
            Collection::VISIBILITY_ADMINISTRATOR,
            Collection::VISIBILITY_MEMBER,
        ];
    }
}

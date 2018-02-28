<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Card;

class VisibilityType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Card::VISIBILITY_PRIVATE,
            Card::VISIBILITY_MEMBER,
            Card::VISIBILITY_PUBLIC,
        ];
    }
}

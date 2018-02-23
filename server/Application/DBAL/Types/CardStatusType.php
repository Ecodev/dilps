<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Card;

class CardStatusType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Card::STATUS_NEW,
            Card::STATUS_EDITED,
            Card::STATUS_REVIEWED,
        ];
    }
}

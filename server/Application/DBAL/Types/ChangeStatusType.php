<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Change;

class ChangeStatusType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Change::STATUS_NEW,
            Change::STATUS_ACCEPTED,
            Change::STATUS_REJECTED,
        ];
    }
}

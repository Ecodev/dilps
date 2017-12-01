<?php

declare(strict_types=1);

namespace Application\DBAL\Types;

use Application\Model\Change;

class ChangeTypeType extends AbstractEnumType
{
    protected function getPossibleValues(): array
    {
        return [
            Change::TYPE_CREATE,
            Change::TYPE_UPDATE,
            Change::TYPE_DELETE,
        ];
    }
}

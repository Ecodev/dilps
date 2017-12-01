<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Change;

class ChangeStatusType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Change::STATUS_NEW => 'A new change request',
            Change::STATUS_ACCEPTED => 'Accepted change request',
            Change::STATUS_REJECTED => 'Rejected change request',
        ];

        parent::__construct($config);
    }
}

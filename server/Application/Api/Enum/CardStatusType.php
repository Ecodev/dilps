<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Card;

class CardStatusType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Card::STATUS_NEW => 'New card',
            Card::STATUS_EDITED => 'Edited card',
            Card::STATUS_REVIEWED => 'Reviewed card',
        ];

        parent::__construct($config);
    }
}

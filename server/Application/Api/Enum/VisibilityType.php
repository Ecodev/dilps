<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Card;

class VisibilityType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Card::VISIBILITY_PRIVATE => 'Visible only to owner',
            Card::VISIBILITY_MEMBER => 'Visible to any logged-in users',
            Card::VISIBILITY_PUBLIC => 'Visible to everyone, included non-logged user',
        ];

        parent::__construct($config);
    }
}

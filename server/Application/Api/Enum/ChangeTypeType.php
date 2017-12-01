<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\Change;

class ChangeTypeType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            Change::TYPE_CREATE => 'Create a new image',
            Change::TYPE_UPDATE => 'Update an existing image',
            Change::TYPE_DELETE => 'Delete an existing image',
        ];

        parent::__construct($config);
    }
}

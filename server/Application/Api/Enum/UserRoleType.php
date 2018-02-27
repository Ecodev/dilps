<?php

declare(strict_types=1);

namespace Application\Api\Enum;

use Application\Model\User;

class UserRoleType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            User::ROLE_STUDENT => 'A student',
            User::ROLE_JUNIOR => 'A junior editor',
            User::ROLE_SENIOR => 'A senior editor',
            User::ROLE_ADMINISTRATOR => 'An administrator',
        ];

        parent::__construct($config);
    }
}

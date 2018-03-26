<?php

declare(strict_types=1);

namespace Application\Api\Enum;

class OrderType extends AbstractEnumType
{
    public function __construct()
    {
        $config = [
            'ASC' => 'Order ascending',
            'DESC' => 'Order descending',
        ];

        parent::__construct($config);
    }
}

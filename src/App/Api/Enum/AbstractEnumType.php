<?php

namespace App\Api\Enum;

use GraphQL\Type\Definition\EnumType;

class AbstractEnumType extends EnumType
{
    public function __construct(array $constants, bool $usePrefix = true)
    {
        if ($usePrefix) {
            $class = new \ReflectionClass(get_class($this));
            $prefix = preg_replace('~Type$~', '', $class->getShortName());
        } else {
            $prefix = '';
        }

        $values = [];
        foreach ($constants as $key => $description) {
            $name = $prefix . $key;
            $values[$name] = [
                'value' => $key,
                'description' => $description,
            ];
        }

        $config = [
            'values' => $values,
        ];

        parent::__construct($config);
    }
}

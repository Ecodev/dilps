<?php

declare(strict_types=1);

namespace App\Api\Input\Filter;

abstract class Filters
{
    /**
     * Build a field for filter if the corresponding class exists
     *
     * @param string $class
     *
     * @return null|array
     */
    public static function build(string $class): ?array
    {
        $reflect = new \ReflectionClass($class);
        $name = $reflect->getShortName();

        $filterTypeClass = 'App\Api\Input\Filter\\' . $name . 'FilterType';
        if (!class_exists($filterTypeClass)) {
            return null;
        }

        return [
            'name' => 'filters',
            'type' => _types()->get($filterTypeClass),
        ];
    }
}

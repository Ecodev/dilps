<?php

declare(strict_types=1);

namespace Application;

use DateTimeImmutable;

abstract class Utility
{
    /**
     * @var DateTimeImmutable
     */
    private static $now;

    /**
     * Returns now, always same value for a single PHP execution
     *
     * @return DateTimeImmutable
     */
    public static function getNow(): DateTimeImmutable
    {
        if (!self::$now) {
            self::$now = new DateTimeImmutable();
        }

        return self::$now;
    }

    /**
     * Returns the short class name of any object, eg: Application\Model\Calendar => Calendar
     *
     * @param object $object
     *
     * @return string
     */
    public static function getShortClassName($object): string
    {
        $reflect = new \ReflectionClass($object);

        return $reflect->getShortName();
    }
}

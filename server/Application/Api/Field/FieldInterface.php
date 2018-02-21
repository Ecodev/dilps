<?php

declare(strict_types=1);

namespace Application\Api\Field;

/**
 * Represent a single field configuration
 */
interface FieldInterface
{
    /**
     * Return the single field configuration, including its name
     *
     * @return array
     */
    public static function build(): array;
}

<?php

declare(strict_types=1);

namespace App\Api\Scalar;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils;

class DateTimeType extends ScalarType
{
    /**
     * @var string
     */
    public $description = 'A date with time and timezone.';

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('c');
        }

        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function parseValue($value)
    {
        if (!is_string($value)) { // quite naive, but after all this is example
            throw new \UnexpectedValueException('Cannot represent value as date: ' . Utils::printSafe($value));
        }

        return new DateTimeImmutable($value);
    }

    /**
     * Parses an externally provided literal value to use as an input (e.g. in Query AST)
     *
     * @param $ast Node
     *
     * @throws Error
     *
     * @return null|string
     */
    public function parseLiteral($ast)
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        if (!($ast instanceof StringValueNode)) {
            throw new Error('Query error: Can only parse strings got: ' . $ast->kind, [$ast]);
        }

        return $ast->value;
    }
}

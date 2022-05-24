<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use Throwable;
use TypeError;

use function get_class;
use function gettype;
use function is_object;
use function is_string;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     06.02.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Assert
{
    public const TYPE_ARRAY           = 'array';
    public const TYPE_BOOLEAN         = 'boolean';
    public const TYPE_DOUBLE          = 'double';
    public const TYPE_FLOAT           = 'double';
    public const TYPE_INTEGER         = 'integer';
    public const TYPE_NULL            = 'NULL';
    public const TYPE_OBJECT          = 'object';
    public const TYPE_RESOURCE        = 'resource';
    public const TYPE_RESOURCE_CLOSED = 'resource (closed)';
    public const TYPE_STRING          = 'string';
    public const TYPE_UNKNOWN         = 'unknown type';

    /**
     * Checks if all specified values are empty.
     */
    public static function empty(...$values): bool
    {
        if (empty($values)) {
            throw new TypeError('At least one argument is required!');
        }

        foreach ($values as $value) {
            if (empty($value) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if all specified values are not empty.
     */
    public static function notEmpty(...$values): bool
    {
        if (empty($values)) {
            throw new TypeError('At least one argument is required!');
        }

        foreach ($values as $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if values are of specified types or instances.
     *
     * @see http://php.net/manual/de/function.gettype.php
     */
    public static function typeOf(string|array $types, ...$values): bool
    {
        if (is_string($types)) {
            $types = [$types];
        }

        foreach ($values as $value) {
            $isObject  = is_object($value);
            $match     = false;
            $valueType = gettype($value);

            // Check if current value is one of the specified types or instances
            foreach ($types as $type) {
                if ($isObject) {
                    $match = $type === static::TYPE_OBJECT || $value instanceof $type;
                } else {
                    $match = $valueType === $type;
                }

                if ($match) {
                    break;
                }
            }

            // Throw type error
            if ($match === false) {
                if ($isObject) {
                    $message   = 'Expected value to be an instance of %s, instance of %s given!';
                    $valueType = get_class($value);
                } else {
                    $message = 'Expected value to be of type %s, %s given!';
                }

                throw new TypeError(sprintf($message, implode(' or ', $types), $valueType));
            }
        }

        return true;
    }

    /**
     * Same as ::typeOf, but without throwing any errors.
     */
    public static function validate(string|array $types, ...$values): bool
    {
        try {
            return static::typeOf($types, ...$values);
        } catch (Throwable) {
            // We won't do anything
        }

        return false;
    }
}

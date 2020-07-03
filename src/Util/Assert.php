<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use Throwable;
use TypeError;

use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function is_string;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     06.02.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Assert
{
    /**
     * Checks if all specified values are empty.
     *
     * @param mixed ...$values
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
     *
     * @param mixed ...$values
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
     * @param array|string $types
     * @param mixed        $values
     * @throws InvalidArgumentException
     *
     * @see http://php.net/manual/de/function.gettype.php
     */
    public static function typeOf($types, ...$values): bool
    {
        if (is_string($types)) {
            $types = [$types];
        }

        if (is_array($types) === false) {
            throw new InvalidArgumentException(
                sprintf('Assertion value must be of type string or array, %s given!', gettype($types))
            );
        }

        foreach ($values as $value) {
            $isObject  = is_object($value);
            $match     = false;
            $valueType = gettype($value);

            // Check if current value is one of the specified types or instances
            foreach ($types as $type) {
                if (
                    ($isObject && ($value instanceof $type || ($type === 'object' && $valueType === $type)))
                    xor ($isObject === false && $valueType === $type)
                ) {
                    $match = true;
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
     *
     * @param array|string $types
     * @param array        $values
     */
    public static function validate($types, ...$values): bool
    {
        try {
            return self::typeOf($types, ...$values);
        } catch (Throwable $t) {
            // We won't do anything
        }

        return false;
    }
}

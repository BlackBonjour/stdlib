<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use Throwable;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     06.02.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Assert
{
    private const MSG_NO_ARGS_RECEIVED = 'At least one argument is required!';
    private const MSG_TYPE_MISMATCH    = 'Assertion value must be of type string or array, %s given!';

    /**
     * Checks if specified values are empty
     *
     * @param mixed ...$values
     * @return boolean
     * @throws TypeError
     */
    public static function empty(...$values): bool
    {
        self::handleInvalidArguments($values);

        foreach ($values as $value) {
            if (empty($value) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $values
     * @throws TypeError
     */
    private static function handleInvalidArguments(array $values): void
    {
        if (empty($values)) {
            throw new TypeError(self::MSG_NO_ARGS_RECEIVED);
        }
    }

    /**
     * Checks if specified values are not empty
     *
     * @param mixed ...$values
     * @return boolean
     * @throws TypeError
     */
    public static function notEmpty(...$values): bool
    {
        self::handleInvalidArguments($values);

        foreach ($values as $value) {
            if (empty($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if values are of specified types or instances
     *
     * @param array|string $types
     * @param mixed        $values
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     *
     * @see http://php.net/manual/de/function.gettype.php
     */
    public static function typeOf($types, ...$values): bool
    {
        if (is_string($types)) {
            $types = [$types];
        }

        if (is_array($types) === false) {
            throw new InvalidArgumentException(sprintf(static::MSG_TYPE_MISMATCH, gettype($types)));
        }

        foreach ($values as $value) {
            $isObject  = is_object($value);
            $match     = false;
            $valueType = gettype($value);

            // Check if current value is one of the specified types or instances
            foreach ($types as $type) {
                if (($isObject && ($value instanceof $type || ($type === 'object' && $valueType === $type)))
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
     * Same as ::typeOf, but without throwing any errors
     *
     * @param array|string $types
     * @param array        $values
     * @return boolean
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

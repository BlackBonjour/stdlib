<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use Throwable;
use TypeError;

/**
 * Utility for easier assertions
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       06.02.2018
 * @package     BlackBonjour\Stdlib\Util
 * @copyright   Copyright (c) 2018 Erick Dyck
 */
class Assert
{
    /**
     * Checks if values are of specified types or instances
     *
     * @param array|string $types
     * @param mixed        $values
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     * @see http://php.net/manual/de/function.gettype.php
     */
    public static function typeOf($types, ...$values) : bool
    {
        if (\is_string($types)) {
            $types = [$types];
        }

        if (\is_array($types) === false) {
            throw new InvalidArgumentException(
                'Assertion value must be of type string or array, ' . \gettype($types) . ' given!'
            );
        }

        foreach ($values as $value) {
            $isObject  = \is_object($value);
            $match     = false;
            $valueType = \gettype($value);

            // Check if current value is one of the specified types or instances
            foreach ($types as $type) {
                if (($isObject && $value instanceof $type) xor ($isObject === false && $valueType === $type)) {
                    $match = true;
                    break;
                }
            }

            // Throw type error
            if ($match === false) {
                if ($isObject) {
                    $message   = 'Expected value to be an instance of %s, instance of %s given!';
                    $valueType = \get_class($value);
                } else {
                    $message = 'Expected value to be of type %s, %s given!';
                }

                throw new TypeError(sprintf($message, implode(' or ', $types), $valueType));
            }
        }

        return true;
    }

    /**
     * Same as ::typeOf, but less aggressive
     *
     * @param array|string $types
     * @param array        $values
     * @return boolean
     */
    public static function validate($types, ...$values) : bool
    {
        try {
            return self::typeOf($types, ...$values);
        } catch (Throwable $t) {
            // We won't do anything
        }

        return false;
    }
}

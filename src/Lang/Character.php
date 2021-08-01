<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Util\Assert;

use function count;
use function is_array;

/**
 * Represents a single character.
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.12.2017
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class Character extends StdObject implements Comparable
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected string $data = '',
    ) {
        if (mb_strlen($this->data) !== 1) {
            throw new InvalidArgumentException('Only one character can be represented!');
        }
    }

    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Determines the number of char values needed to represent the specified
     * character.
     */
    public static function charCount(string|self $char): int
    {
        $stringChar = (string) $char;

        return mb_ord($stringChar, mb_detect_encoding($stringChar)) > 0xFFFF ? 2 : 1;
    }

    /**
     * Returns the unicode code point at specified index.
     *
     * @param static[]|CharSequence $chars
     * @throws InvalidArgumentException
     */
    public static function codePointAt(array|CharSequence $chars, int $index, int $limit = null): int
    {
        // Validate char array
        if (is_array($chars)) {
            foreach ($chars as $char) {
                if (($char instanceof self) === false) {
                    throw new InvalidArgumentException(
                        sprintf('Array must only contain objects of type %s!', self::class)
                    );
                }
            }
        }

        // Validate specified index
        if ($index < 0) {
            throw new InvalidArgumentException('Index cannot be negative!');
        }

        // Validate length
        $length = is_array($chars) ? count($chars) : $chars->length();

        if ($length === 0) {
            throw new InvalidArgumentException('Char array/sequence must not be empty!');
        }

        if ($limit !== null) {
            if ($limit < 0 || $index >= $limit) {
                throw new InvalidArgumentException(
                    'Limit cannot be negative and index must be lower than specified limit!'
                );
            }

            if ($limit > $length) {
                throw new InvalidArgumentException('Limit cannot be greater than char array/sequence!');
            }
        }

        $char       = is_array($chars) ? $chars[$index] : $chars->charAt($index);
        $stringChar = (string) $char;

        return mb_ord($stringChar, mb_detect_encoding($stringChar));
    }

    /**
     * Returns the unicode code point before specified index.
     *
     * @param static[]|CharSequence $chars
     * @throws InvalidArgumentException
     */
    public static function codePointBefore(array|CharSequence $chars, int $index, int $start = null): int
    {
        if ($start !== null && ($start < 0 || $index <= $start)) {
            throw new InvalidArgumentException('Start cannot be negative and index must be greater than start!');
        }

        return static::codePointAt($chars, $index - 1);
    }

    /**
     * Compares specified characters numerically.
     */
    public static function compare(string|self $charA, string|self $charB): int
    {
        return (string) $charA <=> (string) $charB;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function compareTo($object): int
    {
        Assert::typeOf(['string', __CLASS__], $object);

        return strcmp($this->data, (string) $object) <=> 0;
    }

    /**
     * Checks if specified character is lower case.
     *
     * @throws InvalidArgumentException
     */
    public static function isLowerCase(string|self $char): bool
    {
        return static::compare($char, static::toLowerCase($char)) === 0;
    }

    /**
     * Checks if specified character is upper case.
     *
     * @throws InvalidArgumentException
     */
    public static function isUpperCase(string|self $char): bool
    {
        return static::compare($char, static::toUpperCase($char)) === 0;
    }

    /**
     * Converts specified character to lower case.
     *
     * @throws InvalidArgumentException
     */
    public static function toLowerCase(string|self $char): static
    {
        return new static(mb_strtolower((string) $char));
    }

    /**
     * Converts specified character to upper case.
     *
     * @throws InvalidArgumentException
     */
    public static function toUpperCase(string|self $char): static
    {
        return new static(mb_strtoupper((string) $char));
    }

    /**
     * Returns specified value as character.
     *
     * @throws InvalidArgumentException
     */
    public static function valueOf(string|self $char): static
    {
        return new static((string) $char);
    }
}

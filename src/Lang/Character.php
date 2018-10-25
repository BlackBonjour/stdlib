<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Util\Assert;
use TypeError;

/**
 * Represents a single character
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.12.2017
 * @package   BlackBonjour\Stdlib\Lang
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class Character extends StdObject implements Comparable
{
    public const DEFAULT_VALUE = '';

    /** @var string */
    protected $data;

    /**
     * Constructor
     *
     * @param string $char
     * @throws InvalidArgumentException
     */
    public function __construct(string $char = self::DEFAULT_VALUE)
    {
        if (mb_strlen($char) > 1) {
            throw new InvalidArgumentException('Only one character can be represented!');
        }

        $this->data = $char;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Determines the number of char values needed to represent the specified character
     *
     * @param static|string $char
     * @return int
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function charCount($char): int
    {
        self::handleIncomingChar($char);
        $stringChar = (string) $char;

        return mb_ord($stringChar, mb_detect_encoding($stringChar)) > 0xFFFF ? 2 : 1;
    }

    /**
     * Returns the unicode code point at specified index
     *
     * @param CharSequence|static[] $chars
     * @param int                   $index
     * @param int                   $limit
     * @return int
     * @throws InvalidArgumentException
     */
    public static function codePointAt($chars, int $index, int $limit = null): int
    {
        if (\is_array($chars) === false && ($chars instanceof CharSequence) === false) {
            throw new InvalidArgumentException('Only char arrays and sequences can be processed!');
        }

        // Validate char array
        if (\is_array($chars)) {
            $test = reset($chars);

            if ($test === false || ($test instanceof static) === false) {
                throw new InvalidArgumentException('Array must contain char elements!');
            }
        }

        // Validate specified index
        if ($index < 0) {
            throw new InvalidArgumentException('Index cannot be negative!');
        }

        // Validate length
        if ($limit !== null) {
            if ($limit < 0 || $index >= $limit) {
                throw new InvalidArgumentException('Limit cannot be negative and index must be lower than specified limit!');
            }

            $length = \is_array($chars) ? count($chars) : $chars->length();

            if ($limit > $length) {
                throw new InvalidArgumentException('Limit cannot be greater than char array/sequence!');
            }
        }

        $char       = \is_array($chars) ? $chars[$index] : $chars->charAt($index);
        $stringChar = (string) $char;

        return mb_ord($stringChar, mb_detect_encoding($stringChar));
    }

    /**
     * Returns the unicode code point before specified index
     *
     * @param CharSequence|static[] $chars
     * @param int                   $index
     * @param int                   $start
     * @return int
     * @throws InvalidArgumentException
     */
    public static function codePointBefore($chars, int $index, int $start = null): int
    {
        if ($start !== null && ($start < 0 || $index <= $start)) {
            throw new InvalidArgumentException('Start cannot be negative and index must be greater than start!');
        }

        return static::codePointAt($chars, $index - 1);
    }

    /**
     * Compares specified characters numerically
     *
     * @param static|string $charA
     * @param static|string $charB
     * @return int
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function compare($charA, $charB): int
    {
        self::handleIncomingChar($charA, $charB);

        return (string) $charA <=> (string) $charB;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function compareTo($char): int
    {
        self::handleIncomingChar($char);

        return strcmp($this->data, (string) $char) <=> 0;
    }

    /**
     * Validates given character and throws an exception if required
     *
     * @param mixed[] $chars
     * @return void
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    private static function handleIncomingChar(...$chars): void
    {
        foreach ($chars as $char) {
            Assert::typeOf(['string', __CLASS__], $char);

            if (mb_strlen((string) $char) !== 1) {
                throw new InvalidArgumentException('Only one character can be represented!');
            }
        }
    }

    /**
     * Checks if specified character is lower case
     *
     * @param static|string $char
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function isLowerCase($char): bool
    {
        self::handleIncomingChar($char);

        return static::compare($char, static::toLowerCase($char)) === 0;
    }

    /**
     * Checks if specified character is upper case
     *
     * @param static|string $char
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function isUpperCase($char): bool
    {
        self::handleIncomingChar($char);

        return static::compare($char, static::toUpperCase($char)) === 0;
    }

    /**
     * Converts specified character to lower case
     *
     * @param static|string $char
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function toLowerCase($char): self
    {
        self::handleIncomingChar($char);

        return new static(mb_strtolower((string) $char));
    }

    /**
     * Converts specified character to upper case
     *
     * @param static|string $char
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function toUpperCase($char): self
    {
        self::handleIncomingChar($char);

        return new static(mb_strtoupper((string) $char));
    }

    /**
     * Returns specified value as character
     *
     * @param static|string $char
     * @return static
     * @throws InvalidArgumentException
     */
    public static function valueOf($char): self
    {
        if (\is_string($char) || $char instanceof self) {
            return new static((string) $char);
        }

        throw new InvalidArgumentException('Unsupported character type!');
    }
}

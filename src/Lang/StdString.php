<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use ArrayAccess;
use BlackBonjour\Stdlib\Util\Assert;
use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Exception\RuntimeException;
use Countable;
use TypeError;

/**
 * Represents a string of characters
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     22.11.2017
 * @package   BlackBonjour\Stdlib\Lang
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class StdString extends StdObject implements ArrayAccess, CharSequence, Comparable, Countable
{
    public const  DEFAULT_VALUE      = '';
    private const MSG_ILLEGAL_OFFSET = 'Illegal string offset \'%s\'';

    /** @var string */
    protected $data;

    /** @var string */
    protected $encoding;

    /**
     * Constructor
     *
     * @param static|Character[]|string $string
     * @param string                    $encoding
     * @throws InvalidArgumentException
     */
    public function __construct($string = self::DEFAULT_VALUE, string $encoding = null)
    {
        if (\is_string($string)) {
            $this->data = $string;
        } elseif ($string instanceof static) {
            $this->data = (string) $string;
        } elseif (\is_array($string)) {
            foreach ($string as $char) {
                if (($char instanceof Character) === false) {
                    throw new InvalidArgumentException('Only chars are allowed inside array!');
                }

                $this->data .= $char;
            }
        } else {
            throw new InvalidArgumentException('First parameter must by of type string, StdString or an array of Character!');
        }

        $this->encoding = $encoding ?: mb_internal_encoding();
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function charAt(int $index): Character
    {
        if (($index < 0 && $this->length() < (-1 * $index)) || ($index >= 0 && $this->length() <= $index)) {
            throw new OutOfBoundsException('Offset greater or equal string length is not allowed!');
        }

        return new Character(mb_substr($this->data, $index, 1, $this->encoding));
    }

    /**
     * Returns the unicode code point at specified index
     *
     * @param int $index
     * @return int
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function codePointAt(int $index): int
    {
        $char = (string) $this->charAt($index);
        return unpack('N', mb_convert_encoding($char, 'UCS-4BE', $this->encoding))[1];
    }

    /**
     * Returns the unicode code point before specified index
     *
     * @param int $index
     * @return int
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function codePointBefore(int $index): int
    {
        return $this->codePointAt($index - 1);
    }

    /**
     * Compares given string with this string (not multibyte safe)
     *
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function compareTo($string): int
    {
        Assert::typeOf(['string', __CLASS__], $string);
        return strcmp($this->data, (string) $string) <=> 0;
    }

    /**
     * Compares given string with this string case insensitive (not multibyte safe)
     *
     * @param static|string $string
     * @return int
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function compareToIgnoreCase($string): int
    {
        Assert::typeOf(['string', __CLASS__], $string);
        return strcasecmp($this->data, (string) $string) <=> 0;
    }

    /**
     * Concatenates given string to the end of this string
     *
     * @param static|string $string
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function concat($string): self
    {
        Assert::typeOf(['string', __CLASS__], $string);
        return new static($this->data . $string, $this->encoding);
    }

    /**
     * Checks if this string contains specified string
     *
     * @param static|string $string
     * @return boolean
     */
    public function contains($string): bool
    {
        try {
            Assert::typeOf(['string', __CLASS__], $string);
        } catch (InvalidArgumentException|TypeError $t) {
            return false;
        }

        return mb_strpos($this->data, (string) $string, 0, $this->encoding) !== false;
    }

    /**
     * Checks if this string is equal to the given string
     *
     * @param static|string $string
     * @return boolean
     */
    public function contentEquals($string): bool
    {
        try {
            Assert::typeOf(['string', __CLASS__], $string);
        } catch (InvalidArgumentException|TypeError $t) {
            return false;
        }

        return $this->data === (string) $string;
    }

    /**
     * Returns a string that represents the character sequence in the array specified
     *
     * @param static|string|array $charList
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function copyValueOf($charList): self
    {
        Assert::typeOf(['string', 'array', __CLASS__], $charList);

        // Convert to native string before creating a new string object
        $string = '';

        if (\is_array($charList)) {
            foreach ($charList as $value) {
                $string .= static::valueOf($value);
            }
        } else {
            $string = (string) $charList;
        }

        return new static($string);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->length();
    }

    /**
     * Checks if this string ends with specified string
     *
     * @param static|string $string
     * @param boolean       $caseInsensitive
     * @return boolean
     */
    public function endsWith($string, bool $caseInsensitive = false): bool
    {
        try {
            Assert::typeOf(['string', __CLASS__], $string);
        } catch (InvalidArgumentException|TypeError $t) {
            return false;
        }

        $value  = (string) $string;
        $strLen = mb_strlen($value, $this->encoding);
        $length = $this->length();

        if ($strLen > $length) {
            return false;
        }

        return substr_compare($this->data, $value, $length - $strLen, $length, $caseInsensitive) === 0;
    }

    /**
     * Compares this string to the given string case insensitive
     *
     * @param static|string $string
     * @return boolean
     */
    public function equalsIgnoreCase($string): bool
    {
        try {
            Assert::typeOf(['string', __CLASS__], $string);
        } catch (InvalidArgumentException|TypeError $t) {
            return false;
        }

        return strcmp(
            mb_strtolower($this->data, $this->encoding),
            mb_strtolower((string) $string, $this->encoding)
        ) === 0;
    }

    /**
     * Explodes this string by specified delimiter
     *
     * @param static|string $delimiter
     * @return static[]
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws TypeError
     */
    public function explode($delimiter): array
    {
        Assert::typeOf(['string', __CLASS__], $delimiter);

        $response = [];
        $results  = explode((string) $delimiter, $this->data);

        if ($results === false) {
            throw new RuntimeException('An unknown error occurred while splitting string!');
        }

        foreach ($results as $result) {
            $response[] = new static($result, $this->encoding);
        }

        return $response;
    }

    /**
     * Returns a formatted string using the given format and arguments
     *
     * @param static|string $format
     * @param mixed         ...$args
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public static function format($format, ...$args): self
    {
        Assert::typeOf(['string', __CLASS__], $format);
        return new static(sprintf((string) $format, ...$args));
    }

    /**
     * Encodes this string into a sequence of bytes
     *
     * @return int[]
     */
    public function getBytes(): array
    {
        return array_values(unpack('C*', $this->data));
    }

    /**
     * Copies characters from this string into the destination array
     *
     * @param int         $begin
     * @param int         $end
     * @param Character[] $destination
     * @param int         $dstBegin
     * @return void
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function getChars(int $begin, int $end, array &$destination, int $dstBegin): void
    {
        $length = $end - $begin + 1;

        for ($i = 0; $i < $length; $i++) {
            $destination[$dstBegin + $i] = $this->charAt($begin + $i);
        }
    }

    /**
     * Returns the index within this string of the first occurrence of the specified string
     *
     * @param static|string $string
     * @param int           $offset
     * @return int
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function indexOf($string, int $offset = 0): int
    {
        Assert::typeOf(['string', __CLASS__], $string);
        $pos = mb_strpos($this->data, (string) $string, $offset, $this->encoding);

        return $pos > -1 ? $pos : -1;
    }

    /**
     * Checks if this string is empty
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Returns the index within this string of the last occurrence of the specified string
     *
     * @param static|string $string
     * @param int           $offset
     * @return int
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function lastIndexOf($string, int $offset = 0): int
    {
        Assert::typeOf(['string', __CLASS__], $string);
        $pos = mb_strrpos($this->data, (string) $string, $offset, $this->encoding);

        return $pos > -1 ? $pos : -1;
    }

    /**
     * @inheritdoc
     */
    public function length(): int
    {
        return mb_strlen($this->data, $this->encoding);
    }

    /**
     * Checks if this string matches the given regex pattern
     *
     * @param static|string $pattern
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function matches($pattern): bool
    {
        Assert::typeOf(['string', __CLASS__], $pattern);
        return preg_match((string) $pattern, $this->data) === 1;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        if (is_numeric($offset) === false) {
            return false;
        }

        $offset = (int) $offset;
        return $offset < 0 ? $this->length() >= (-1 * $offset) : $this->length() > $offset;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (is_numeric($offset) === false) {
            trigger_error(sprintf('Illegal string offset \'%s\'', $offset), E_USER_WARNING);
            return $this->charAt(0);
        }

        return $this->charAt((int) $offset);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        if (is_numeric($offset) === false) {
            trigger_error(sprintf(static::MSG_ILLEGAL_OFFSET, $offset), E_USER_WARNING);
            return;
        }

        $length = $this->length();
        $offset = (int) $offset;

        if ($offset < 0) {
            // Convert to positive offset
            $tmp = $length + $offset;

            if ($tmp < 0) {
                trigger_error(sprintf(static::MSG_ILLEGAL_OFFSET, $offset), E_USER_WARNING);
                return;
            }

            $offset = $tmp;
        }

        $prefix       = $this->substr(0, $offset);
        $suffix       = '';
        $suffixOffset = $offset + mb_strlen($value);

        if ($length < $offset) {
            $prefix .= str_repeat(' ', $offset - $length);
        }

        if ($suffixOffset < $length) {
            $suffix = $this->substr($suffixOffset);
        }

        $this->data = $prefix . $value . $suffix;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
        trigger_error('Cannot unset string offsets', E_USER_ERROR);
    }

    /**
     * Checks if two string regions are equal
     *
     * @param int           $offset
     * @param static|string $string
     * @param int           $strOffset
     * @param int           $len
     * @param boolean       $ignoreCase
     * @return boolean
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function regionMatches(int $offset, $string, int $strOffset, int $len, bool $ignoreCase = false): bool
    {
        Assert::typeOf(['string', __CLASS__], $string);
        $strLen = \is_string($string) ? mb_strlen($string, $this->encoding) : $string->length();

        if ($offset < 0 || $strOffset < 0 || ($strOffset + $len) > $strLen || ($offset + $len) > $this->length()) {
            return false;
        }

        $stringA = mb_substr($this->data, $offset, $len, $this->encoding);
        $stringB = mb_substr((string) $string, $strOffset, $len, $this->encoding);

        // Compare strings
        if ($ignoreCase) {
            $result = strcmp(mb_strtolower($stringA, $this->encoding), mb_strtolower($stringB, $this->encoding));
        } else {
            $result = strcmp($stringA, $stringB);
        }

        return $result === 0;
    }

    /**
     * Replaces all occurrences of $old in this string with $new
     *
     * @param static|string $old
     * @param static|string $new
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function replace($old, $new): self
    {
        Assert::typeOf(['string', __CLASS__], $old, $new);
        return new static(str_replace((string) $old, (string) $new, $this->data), $this->encoding);
    }

    /**
     * Replaces each substring of this string that matches the given regex pattern with the specified replacement
     *
     * @param static|string $pattern
     * @param static|string $replacement
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function replaceAll($pattern, $replacement): self
    {
        Assert::typeOf(['string', __CLASS__], $pattern, $replacement);
        $result = preg_replace($pattern, $replacement, $this->data);

        return new static($result ?: $this->data, $this->encoding);
    }

    /**
     * Replaces the first substring of this string that matches the given regex pattern with the specified replacement
     *
     * @param static|string $pattern
     * @param static|string $replacement
     * @return static
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function replaceFirst($pattern, $replacement): self
    {
        Assert::typeOf(['string', __CLASS__], $pattern, $replacement);
        $result = preg_replace($pattern, $replacement, $this->data, 1);

        return new static($result ?: $this->data, $this->encoding);
    }

    /**
     * Splits this string around matches of the given regex pattern
     *
     * @param static|string $pattern
     * @param int           $limit
     * @return static[]
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws TypeError
     */
    public function split($pattern, int $limit = -1): array
    {
        Assert::typeOf(['string', __CLASS__], $pattern);

        $response = [];
        $results  = preg_split((string) $pattern, $this->data, $limit);

        if ($results === false) {
            throw new RuntimeException('An unknown error occurred while splitting string!');
        }

        foreach ($results as $result) {
            $response[] = new static($result, $this->encoding);
        }

        return $response;
    }

    /**
     * Checks if this string starts with specified string
     *
     * @param static|string $string
     * @param int           $offset
     * @return boolean
     */
    public function startsWith($string, int $offset = 0): bool
    {
        try {
            Assert::typeOf(['string', __CLASS__], $string);
        } catch (InvalidArgumentException|TypeError $t) {
            return false;
        }

        return mb_strpos($this->data, (string) $string, $offset, $this->encoding) === 0;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function subSequence(int $begin, int $end): array
    {
        if ($begin < 0 || $end > $this->length() - 1) {
            throw new OutOfBoundsException(
                'Specified begin index is negative and/or end index is greater or equal string length!'
            );
        }

        $charList = [];
        $this->getChars($begin, $end, $charList, 0);

        return $charList;
    }

    /**
     * Returns a new string object that is a substring of this string
     *
     * @param int $start
     * @param int $length
     * @return static
     * @throws InvalidArgumentException
     */
    public function substr(int $start, int $length = null): self
    {
        if ($start < 0) {
            throw new InvalidArgumentException('Negative index is not allowed!');
        }

        return new static(mb_substr($this->data, $start, $length, $this->encoding), $this->encoding);
    }

    /**
     * Returns a new string object that is a substring of this string (equivalent to java.lang.String)
     *
     * @param int $begin
     * @param int $end
     * @return static
     * @throws InvalidArgumentException
     */
    public function substring(int $begin, int $end = null): self
    {
        return $this->substr($begin, $end ? $end - $begin + 1 : null);
    }

    /**
     * Converts this string to a character array
     *
     * @return Character[]
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function toCharArray(): array
    {
        $charList = [];
        $this->getChars(0, $this->length() - 1, $charList, 0);

        return $charList;
    }

    /**
     * Converts all characters in this string to lower case
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function toLowerCase(): self
    {
        return new static(mb_strtolower($this->data, $this->encoding), $this->encoding);
    }

    /**
     * Converts all characters in this string to upper case
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function toUpperCase(): self
    {
        return new static(mb_strtoupper($this->data, $this->encoding), $this->encoding);
    }

    /**
     * Removes leading and ending whitespaces in this string
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function trim(): self
    {
        return new static(trim($this->data));
    }

    /**
     * Returns the string representation of the given value
     *
     * @param mixed $value
     * @return static
     * @throws InvalidArgumentException
     */
    public static function valueOf($value): self
    {
        $strVal = null;

        switch (\gettype($value)) {
            case 'object':
                if ($value instanceof StdObject || (\is_object($value) && method_exists($value, '__toString'))) {
                    $strVal = (string) $value;
                }
                break;
            case 'boolean':
                $strVal = $value ? 'true' : 'false';
                break;
            case 'double':
            case 'integer':
            case 'string':
                $strVal = (string) $value;
                break;
        }

        if ($strVal === null) {
            throw new InvalidArgumentException('Unsupported value type!');
        }

        return new static($strVal);
    }
}

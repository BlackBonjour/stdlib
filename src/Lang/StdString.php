<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use ArrayAccess;
use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Exception\RuntimeException;
use BlackBonjour\Stdlib\Util\Assert;
use Countable;
use Stringable;

use function gettype;
use function is_array;
use function is_object;
use function is_string;

/**
 * Represents a string of characters.
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     22.11.2017
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class StdString extends StdObject implements ArrayAccess, CharSequence, Comparable, Countable
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        protected string $data = '',
        protected ?string $encoding = null,
    ) {
        if ($this->encoding === null) {
            $this->encoding = mb_internal_encoding();

            if (is_string($this->encoding) === false) {
                throw new InvalidArgumentException('Invalid encoding received!');
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function createFromArrayOfChar(array $chars): static
    {
        $data = '';

        foreach ($chars as $char) {
            if (($char instanceof Character) === false) {
                throw new InvalidArgumentException(
                    sprintf('Array of %s is required!', Character::class)
                );
            }

            $data .= $char;
        }

        return new static($data);
    }

    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * @inheritDoc
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
     * Returns the unicode code point at specified index.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function codePointAt(int $index): int
    {
        return mb_ord((string) $this->charAt($index), $this->encoding);
    }

    /**
     * Returns the unicode code point before specified index.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function codePointBefore(int $index): int
    {
        return $this->codePointAt($index - 1);
    }

    /**
     * Compares given string with this string (not multibyte safe).
     *
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function compareTo($object): int
    {
        Assert::typeOf(['string', __CLASS__], $object);

        return strcmp($this->data, (string) $object) <=> 0;
    }

    /**
     * Compares given string with this string case insensitive (not multibyte
     * safe).
     */
    public function compareToIgnoreCase(string|self $string): int
    {
        return strcasecmp($this->data, (string) $string) <=> 0;
    }

    /**
     * Concatenates given string to the end of this string.
     *
     * @throws InvalidArgumentException
     */
    public function concat(string|self $string): static
    {
        return new static($this->data . $string, $this->encoding);
    }

    /**
     * Checks if this string contains specified string.
     */
    public function contains(string|self $string): bool
    {
        return mb_strpos($this->data, (string) $string, 0, $this->encoding) !== false;
    }

    /**
     * Checks if this string is equal to the given string.
     */
    public function contentEquals(string|self $string): bool
    {
        return $this->data === (string) $string;
    }

    /**
     * Returns a string that represents the character sequence in the array
     * specified.
     *
     * @throws InvalidArgumentException
     */
    public static function copyValueOf(string|array|self $charList): static
    {
        $string = '';

        if (is_array($charList)) {
            foreach ($charList as $value) {
                $string .= static::valueOf($value);
            }
        } else {
            $string = (string) $charList;
        }

        return new static($string);
    }

    public function count(): int
    {
        return $this->length();
    }

    /**
     * Checks if this string ends with specified string.
     */
    public function endsWith(string|self $string, bool $caseInsensitive = false): bool
    {
        $value  = (string) $string;
        $strLen = mb_strlen($value, $this->encoding);
        $length = $this->length();

        if ($strLen > $length) {
            return false;
        }

        return substr_compare($this->data, $value, $length - $strLen, $length, $caseInsensitive) === 0;
    }

    /**
     * Compares this string to the given string case insensitive.
     */
    public function equalsIgnoreCase(string|self $string): bool
    {
        $result = strcmp(
            mb_strtolower($this->data, $this->encoding),
            mb_strtolower((string) $string, $this->encoding)
        );

        return $result === 0;
    }

    /**
     * Explodes this string by specified delimiter.
     *
     * @return static[]
     * @throws InvalidArgumentException
     */
    public function explode(string|self $delimiter): array
    {
        $delimiter = (string) $delimiter;

        if ($delimiter === '') {
            throw new InvalidArgumentException('Empty delimiter!');
        }

        $response = [];
        $results  = explode($delimiter, $this->data);

        foreach ($results as $result) {
            $response[] = new static($result, $this->encoding);
        }

        return $response;
    }

    /**
     * Returns a formatted string using the given format and arguments.
     *
     * @throws InvalidArgumentException
     */
    public static function format(string|self $format, ...$args): static
    {
        return new static(sprintf((string) $format, ...$args));
    }

    /**
     * Encodes this string into a sequence of bytes.
     *
     * @return int[]
     */
    public function getBytes(): array
    {
        return array_values(unpack('C*', $this->data));
    }

    /**
     * Copies characters from this string into the destination array.
     *
     * @param Character[] $destination
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
     * Returns the index within this string of the first occurrence of the
     * specified string.
     */
    public function indexOf(string|self $string, int $offset = 0): int
    {
        $pos = mb_strpos($this->data, (string) $string, $offset, $this->encoding);

        return $pos > -1 ? $pos : -1;
    }

    /**
     * Checks if this string is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Returns the index within this string of the last occurrence of the
     * specified string.
     */
    public function lastIndexOf(string|self $string, int $offset = 0): int
    {
        $pos = mb_strrpos($this->data, (string) $string, $offset, $this->encoding);

        return $pos > -1 ? $pos : -1;
    }

    public function length(): int
    {
        return mb_strlen($this->data, $this->encoding);
    }

    /**
     * Checks if this string matches the given regex pattern.
     */
    public function matches(string|self $pattern): bool
    {
        return preg_match((string) $pattern, $this->data) === 1;
    }

    public function offsetExists($offset): bool
    {
        if (is_numeric($offset) === false) {
            return false;
        }

        $offset = (int) $offset;

        return $offset < 0 ? $this->length() >= (-1 * $offset) : $this->length() > $offset;
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset): Character
    {
        if (is_numeric($offset) === false) {
            trigger_error(sprintf('Illegal string offset \'%s\'', $offset), E_USER_WARNING);

            return $this->charAt(0);
        }

        return $this->charAt((int) $offset);
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        if (is_numeric($offset) === false) {
            trigger_error(sprintf('Illegal string offset \'%s\'', $offset), E_USER_WARNING);

            return;
        }

        $length = $this->length();
        $offset = (int) $offset;

        if ($offset < 0) {
            // Convert to positive offset
            $tmp = $length + $offset;

            if ($tmp < 0) {
                trigger_error(sprintf('Illegal string offset \'%s\'', $offset), E_USER_WARNING);

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

    public function offsetUnset($offset): void
    {
        trigger_error('Cannot unset string offsets', E_USER_ERROR);
    }

    /**
     * Checks if two string regions are equal.
     */
    public function regionMatches(
        int $offset,
        string|self $string,
        int $strOffset,
        int $len,
        bool $ignoreCase = false
    ): bool {
        $strLen = is_string($string) ? mb_strlen($string, $this->encoding) : $string->length();

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
     * Replaces all occurrences of $old in this string with $new.
     *
     * @throws InvalidArgumentException
     */
    public function replace(string|self $old, string|self $new): static
    {
        return new static(str_replace((string) $old, (string) $new, $this->data), $this->encoding);
    }

    /**
     * Replaces each substring of this string that matches the given regex
     * pattern with the specified replacement.
     *
     * @throws InvalidArgumentException
     */
    public function replaceAll(string|self $pattern, string|self $replacement): static
    {
        $result = preg_replace((string) $pattern, (string) $replacement, $this->data);

        return new static($result ?: $this->data, $this->encoding);
    }

    /**
     * Replaces the first substring of this string that matches the given regex
     * pattern with the specified replacement.
     *
     * @throws InvalidArgumentException
     */
    public function replaceFirst(string|self $pattern, string|self $replacement): static
    {
        $result = preg_replace((string) $pattern, (string) $replacement, $this->data, 1);

        return new static($result ?: $this->data, $this->encoding);
    }

    /**
     * Splits this string around matches of the given regex pattern.
     *
     * @return static[]
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function split(string|self $pattern, int $limit = -1): array
    {
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
     * Checks if this string starts with specified string.
     */
    public function startsWith(string|self $string, int $offset = 0): bool
    {
        return mb_strpos($this->data, (string) $string, $offset, $this->encoding) === 0;
    }

    /**
     * @inheritDoc
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
     * Returns a new string object that is a substring of this string.
     *
     * @throws InvalidArgumentException
     */
    public function substr(int $start, int $length = null): static
    {
        if ($start < 0) {
            throw new InvalidArgumentException('Negative index is not allowed!');
        }

        return new static(mb_substr($this->data, $start, $length, $this->encoding), $this->encoding);
    }

    /**
     * Returns a new string object that is a substring of this string
     * (equivalent to java.lang.String).
     *
     * @throws InvalidArgumentException
     */
    public function substring(int $begin, int $end = null): static
    {
        return $this->substr($begin, $end ? $end - $begin + 1 : null);
    }

    /**
     * Converts this string to a character array.
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
     * Converts all characters in this string to lower case.
     *
     * @throws InvalidArgumentException
     */
    public function toLowerCase(): static
    {
        return new static(mb_strtolower($this->data, $this->encoding), $this->encoding);
    }

    /**
     * Converts all characters in this string to upper case.
     *
     * @throws InvalidArgumentException
     */
    public function toUpperCase(): static
    {
        return new static(mb_strtoupper($this->data, $this->encoding), $this->encoding);
    }

    /**
     * Removes leading and ending whitespaces in this string.
     *
     * @throws InvalidArgumentException
     */
    public function trim(): static
    {
        return new static(trim($this->data));
    }

    /**
     * Returns the string representation of the given value.
     *
     * @throws InvalidArgumentException
     */
    public static function valueOf($value): static
    {
        $strVal = null;

        switch (gettype($value)) {
            case 'object':
                if ($value instanceof Stringable || (is_object($value) && method_exists($value, '__toString'))) {
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

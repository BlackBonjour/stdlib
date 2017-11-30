<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;

/**
 * Represents character strings
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       22.11.2017
 * @package     BlackBonjour\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 */
class StdString extends Object
{
    const DEFAULT_VALUE = '';

    /** @var string  */
    protected $data = self::DEFAULT_VALUE;

    /**
     * Constructor
     *
     * @param   string  $string
     */
    public function __construct(string $string = self::DEFAULT_VALUE)
    {
        $this->data = $string;
    }

    /**
     * This strings value
     *
     * @return  string
     */
    public function __toString() : string
    {
        return $this->data;
    }

    /**
     * Returns the character at specified index
     *
     * @param   int $index
     * @return  string
     * @throws  OutOfBoundsException
     */
    public function charAt(int $index) : string
    {
        if ($index < 0 || $index > $this->length() - 1) {
            throw new OutOfBoundsException('Negative values and values greater or equal object length are not allowed!');
        }

        return mb_substr($this->data, $index, 1);
    }

    /**
     * Compares given string with this string
     *
     * @param   StdString|string $string
     * @return  int
     * @throws  InvalidArgumentException
     */
    public function compareTo($string) : int
    {
        self::handleIncomingString($string);
        return strcmp($this->data, (string) $string) <=> 0;
    }

    /**
     * Compares given string with this string case insensitive
     *
     * @param   StdString|string $string
     * @return  int
     * @throws  InvalidArgumentException
     */
    public function compareToIgnoreCase($string) : int
    {
        self::handleIncomingString($string);
        return strcasecmp($this->data, (string) $string) <=> 0;
    }

    /**
     * Concatenates given string to the end of this string
     *
     * @param   StdString|string $string
     * @return  $this
     * @throws  InvalidArgumentException
     */
    public function concat($string)
    {
        self::handleIncomingString($string);
        $this->data .= (string) $string;

        return $this;
    }

    /**
     * Checks if this string contains specified string
     *
     * @param   StdString|string $string
     * @return  boolean
     */
    public function contains($string) : bool
    {
        if (self::validateString($string) === false) {
            return false;
        }

        return mb_strpos($this->data, (string) $string) !== false;
    }

    /**
     * Checks if this string is equal to the given string
     *
     * @param   StdString|string $string
     * @return  boolean
     */
    public function contentEquals($string) : bool
    {
        if (self::validateString($string) === false) {
            return false;
        }

        return $this->data === (string) $string;
    }

    /**
     * Returns a string that represents the character sequence in the array specified
     *
     * @param   StdString|string|array  $charList
     * @return  StdString
     * @throws  InvalidArgumentException
     */
    public static function copyValueOf($charList) : self
    {
        if (\is_array($charList) === false && self::validateString($charList) === false) {
            throw new InvalidArgumentException('Given value must be of type string, array or a string related object!');
        }

        // Convert to native string before creating a new string object instance
        $string = '';

        if (\is_array($charList)) {
            foreach ($charList as $value) {
                $string .= (string) self::valueOf($value);
            }
        } else {
            $string = (string) $charList;
        }

        return new self($string);
    }

    /**
     * Checks if this string ends with specified string
     *
     * @param   StdString|string    $string
     * @return  boolean
     */
    public function endsWith($string) : bool
    {
        if (self::validateString($string) === false) {
            return false;
        }

        $value  = (string) $string;
        $strLen = mb_strlen($value);
        $length = $this->length();

        return $strLen > $length ? false : substr_compare($this->data, $value, $length - $strLen, $length) === 0;
    }

    /**
     * Compares this string to the given string case insensitive
     *
     * @param   StdString|string    $string
     * @return  boolean
     */
    public function equalsIgnoreCase($string) : bool
    {
        if (self::validateString($string) === false) {
            return false;
        }

        return strcmp(mb_strtolower($this->data), mb_strtolower((string) $string)) === 0;
    }

    /**
     * Explodes this string by specified delimiter
     *
     * @param   StdString|string    $delimiter
     * @return  StdString[]
     * @throws  InvalidArgumentException
     * @throws  RuntimeException
     */
    public function explode($delimiter) : array
    {
        self::handleIncomingString($delimiter);

        $response = [];
        $results  = explode((string) $delimiter, $this->data);

        if ($results === false) {
            throw new RuntimeException('An unknown error occurred while splitting string!');
        }

        foreach ($results as $result) {
            $response[] = new self($result);
        }

        return $response;
    }

    /**
     * Returns a formatted string using the given format and arguments
     *
     * @param   StdString|string    $format
     * @param   mixed               ...$args
     * @return  self
     * @throws  InvalidArgumentException
     */
    public static function format($format, ...$args) : self
    {
        self::handleIncomingString($format);
        return new self(sprintf((string) $format, ...$args));
    }

    /**
     * Encodes this string into a sequence of bytes
     *
     * @return  int[]
     * @todo    Add support for different encodings!
     */
    public function getBytes() : array
    {
        return array_values(unpack('C*', $this->data));
    }

    /**
     * Copies characters from this string into the destination array
     *
     * @param   int         $begin
     * @param   int         $end
     * @param   string[]    $destination
     * @param   int         $dstBegin
     * @return  void
     * @throws  OutOfBoundsException
     */
    public function getChars(int $begin, int $end, array &$destination, int $dstBegin)
    {
        $length = $end - $begin + 1;

        for ($i = 0; $i < $length; $i++) {
            $destination[$dstBegin + $i] = $this->charAt($begin + $i);
        }
    }

    /**
     * Validates given string and throws an exception if required
     *
     * @param   mixed[] $strings
     * @return  void
     * @throws  InvalidArgumentException
     */
    private static function handleIncomingString(...$strings)
    {
        foreach ($strings as $string) {
            if (self::validateString($string) === false) {
                throw new InvalidArgumentException('Given value must be of type string or a string related object!');
            }
        }
    }

    /**
     * Returns the index within this string of the first occurrence of the specified string
     *
     * @param   StdString|string    $string
     * @param   int                 $offset
     * @return  int
     * @throws  InvalidArgumentException
     */
    public function indexOf($string, int $offset = 0) : int
    {
        self::handleIncomingString($string);
        $pos = mb_strpos($this->data, (string) $string, $offset);

        return $pos > -1 ? $pos : -1;
    }

    /**
     * Checks if this string is empty
     *
     * @return  boolean
     */
    public function isEmpty() : bool
    {
        return empty($this->data);
    }

    /**
     * Returns the index within this string of the last occurrence of the specified string
     *
     * @param   StdString|string    $string
     * @param   int $offset
     * @return  int
     * @throws  InvalidArgumentException
     */
    public function lastIndexOf($string, int $offset = 0) : int
    {
        self::handleIncomingString($string);
        $pos = mb_strrpos($this->data, (string) $string, $offset);

        return $pos > -1 ? $pos : -1;
    }

    /**
     * Returns the length of this string
     *
     * @return  int
     */
    public function length() : int
    {
        return mb_strlen($this->data);
    }

    /**
     * Checks if this string matches the given regex pattern
     *
     * @param   StdString|string    $pattern
     * @return  boolean
     * @throws  InvalidArgumentException
     */
    public function matches($pattern) : bool
    {
        self::handleIncomingString($pattern);
        return preg_match((string) $pattern, $this->data) === 1;
    }

    /**
     * Checks if two string regions are equal
     *
     * @param   int                 $offset
     * @param   StdString|string    $string
     * @param   int                 $strOffset
     * @param   int                 $len
     * @param   boolean             $ignoreCase
     * @return  boolean
     * @throws  InvalidArgumentException
     */
    public function regionMatches(int $offset, $string, int $strOffset, int $len, bool $ignoreCase = false) : bool
    {
        self::handleIncomingString($string);
        $strLen = \is_string($string) ? mb_strlen($string) : $string->length();

        if ($offset < 0 || $strOffset < 0 || ($strOffset + $len) > $strLen || ($offset + $len) > $this->length()) {
            return false;
        }

        $stringA = mb_substr($this->data, $offset, $len);
        $stringB = mb_substr((string) $string, $strOffset, $len);

        // Compare strings
        if ($ignoreCase) {
            $result = strcmp(mb_strtolower($stringA), mb_strtolower($stringB));
        } else {
            $result = strcmp($stringA, $stringB);
        }

        return $result === 0;
    }

    /**
     * Replaces all occurrences of $old in this string with $new
     *
     * @param   StdString|string    $old
     * @param   StdString|string    $new
     * @return  $this
     * @throws  InvalidArgumentException
     */
    public function replace($old, $new)
    {
        self::handleIncomingString($old, $new);
        $this->data = str_replace((string) $old, (string) $new, $this->data);

        return $this;
    }

    /**
     * Replaces each substring of this string that matches the given regex pattern with the specified replacement
     *
     * @param   StdString|string    $pattern
     * @param   StdString|string    $replacement
     * @return  $this
     * @throws  InvalidArgumentException
     */
    public function replaceAll($pattern, $replacement)
    {
        self::handleIncomingString($pattern, $replacement);
        $result = preg_replace($pattern, $replacement, $this->data);

        if ($result !== null) {
            $this->data = $result;
        }

        return $this;
    }

    /**
     * Replaces the first substring of this string that matches the given regex pattern with the specified replacement
     *
     * @param   StdString|string    $pattern
     * @param   StdString|string    $replacement
     * @return  $this
     * @throws  InvalidArgumentException
     */
    public function replaceFirst($pattern, $replacement)
    {
        self::handleIncomingString($pattern, $replacement);
        $result = preg_replace($pattern, $replacement, $this->data, 1);

        if ($result !== null) {
            $this->data = $result;
        }

        return $this;
    }

    /**
     * Splits this string around matches of the given regex pattern
     *
     * @param   StdString|string    $pattern
     * @param   int                 $limit
     * @return  StdString[]
     * @throws  InvalidArgumentException
     * @throws  RuntimeException
     */
    public function split($pattern, int $limit = -1) : array
    {
        self::handleIncomingString($pattern);

        $response = [];
        $results  = preg_split((string) $pattern, $this->data, $limit);

        if ($results === false) {
            throw new RuntimeException('An unknown error occurred while splitting string!');
        }

        foreach ($results as $result) {
            $response[] = new self($result);
        }

        return $response;
    }

    /**
     * Checks if this string starts with specified string
     *
     * @param   StdString|string    $string
     * @param   int                 $offset
     * @return  boolean
     */
    public function startsWith($string, int $offset = 0) : bool
    {
        if (self::validateString($string) === false) {
            return false;
        }

        return mb_strpos($this->data, (string) $string, $offset) === 0;
    }

    /**
     * Returns an array containing characters between specified start index and end index
     *
     * @param   int $begin
     * @param   int $end
     * @return  string[]
     * @throws  OutOfBoundsException
     */
    public function subSequence(int $begin, int $end) : array
    {
        if ($begin < 0 || $end > $this->length() - 1) {
            throw new OutOfBoundsException('Specified begin index is negative and/or end index is greater or equal string length!');
        }

        $charList = [];
        $this->getChars($begin, $end, $charList, 0);

        return $charList;
    }

    /**
     * Returns a new string object that is a substring of this string
     *
     * @param   int $begin
     * @param   int $end
     * @return  StdString
     * @throws  InvalidArgumentException
     */
    public function substring(int $begin, int $end = null) : self
    {
        if ($begin < 0) {
            throw new InvalidArgumentException('Negative index is not allowed!');
        }

        return new self(mb_substr($this->data, $begin, $end));
    }

    /**
     * Converts this string to a new string (character) array
     *
     * @return  string[]
     * @throws  OutOfBoundsException
     */
    public function toCharArray() : array
    {
        $charList = [];
        $this->getChars(0, $this->length(), $charList, 0);

        return $charList;
    }

    /**
     * Converts all characters in this string to lower case
     *
     * @return  $this
     */
    public function toLowerCase()
    {
        $this->data = mb_strtolower($this->data);
        return $this;
    }

    /**
     * Converts all characters in this string to upper case
     *
     * @return  $this
     */
    public function toUpperCase()
    {
        $this->data = mb_strtoupper($this->data);
        return $this;
    }

    /**
     * Removes leading and ending whitespaces in this string
     *
     * @return  $this
     */
    public function trim()
    {
        $this->data = trim($this->data);
        return $this;
    }

    /**
     * Validates given string
     *
     * @param   StdString|string $string
     * @return  boolean
     */
    private static function validateString($string) : bool
    {
        return \is_string($string) || $string instanceof self;
    }

    /**
     * Returns the string representation of the given value
     *
     * @param   mixed   $value
     * @return  StdString
     */
    public static function valueOf($value) : self
    {
        $strVal = '';

        switch (\gettype($value)) {
            case 'object':
                if ($value instanceof Object) {
                    $strVal = (string) $value;
                }
                break;
            case 'array':
            case 'resource':
            case 'NULL':
                break;
            default:
                $strVal = (string) $value;
                break;
        }

        return new self($strVal);
    }
}

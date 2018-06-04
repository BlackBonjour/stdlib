<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayAccess;
use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Lang\StdObject;
use Countable;
use Iterator;
use TypeError;

/**
 * Arrays
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.06.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Arrays extends StdObject implements ArrayAccess, Countable, Iterator
{
    public const MSG_ILLEGAL_ARGUMENT_TYPE         = 'Expected argument %d to be numeric, %s given!';
    public const MSG_NEGATIVE_ARGUMENT_NOT_ALLOWED = 'Argument %d must be %d or higher!';
    public const MSG_UNDEFINED_OFFSET              = 'Offset %d does not exist!';

    /** @var array */
    protected $values = [];

    /**
     * Constructor
     *
     * @param array $values
     * @throws InvalidArgumentException
     */
    public function __construct(array $values = [])
    {
        if ($values !== []) {
            $this->pushAll($values);
        }
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     * Fills array with specified value
     *
     * @param mixed $newValue
     * @return $this
     */
    public function fill($newValue) : self
    {
        foreach ($this->values as $index => $value) {
            $this->values[$index] = $newValue;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function key() : int
    {
        return key($this->values);
    }

    /**
     * @inheritdoc
     */
    public function next() : void
    {
        next($this->values);
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetExists($offset) : bool
    {
        if (is_numeric($offset) === false) {
            throw new TypeError(self::MSG_ILLEGAL_ARGUMENT_TYPE, 1, gettype($offset));
        }

        return array_key_exists($offset, $this->values);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function offsetGet($offset)
    {
        if (is_numeric($offset) === false) {
            throw new TypeError(self::MSG_ILLEGAL_ARGUMENT_TYPE, 1, gettype($offset));
        }

        if ($this->offsetExists($offset) === false) {
            throw new OutOfBoundsException(self::MSG_UNDEFINED_OFFSET, $offset);
        }

        return $this->values[$offset];
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value) : void
    {
        $this->push($value);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function offsetUnset($offset) : void
    {
        if ($this->offsetExists($offset) === false) {
            throw new OutOfBoundsException(sprintf(self::MSG_UNDEFINED_OFFSET, $offset));
        }

        unset($this->values[$offset]);

        $x = null;
    }

    /**
     * Pushes specified value into array
     *
     * @param mixed    $value
     * @param int|null $repeat
     * @return Arrays
     * @throws InvalidArgumentException
     */
    public function push($value, int $repeat = null) : self
    {
        if ($repeat !== null && $repeat <= 0) {
            throw new InvalidArgumentException(sprintf(self::MSG_NEGATIVE_ARGUMENT_NOT_ALLOWED, 2, 1));
        }

        for ($i = 0; $i < ($repeat ?: 1); $i++) {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * Pushes multiple values to array
     *
     * @param array $values
     * @return Arrays
     * @throws InvalidArgumentException
     */
    public function pushAll(array $values) : self
    {
        if ($values !== []) {
            foreach ($values as $value) {
                $this->push($value);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rewind() : void
    {
        reset($this->values);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function valid() : bool
    {
        return $this->key() !== null;
    }
}

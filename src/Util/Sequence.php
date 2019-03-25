<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Lang\StdObject;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.06.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Sequence extends StdObject implements MapInterface
{
    private const MSG_ILLEGAL_ARGUMENT_TYPE         = 'Expected argument %d to be numeric, %s given!';
    private const MSG_NEGATIVE_ARGUMENT_NOT_ALLOWED = 'Argument %d must be %d or higher!';
    private const MSG_UNDEFINED_OFFSET              = 'Offset %d does not exist!';

    /** @var array */
    private $values = [];

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->values = [];
    }

    /**
     * @param mixed $key
     * @return boolean
     * @throws TypeError
     */
    public function containsKey($key): bool
    {
        $this->handleInvalidKey($key);

        return array_key_exists($key, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function containsValue($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * Returns a new sequence from specified array
     *
     * @param array $array
     * @return static
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $array): self
    {
        return (new self)->pushAll($array);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return $this->size();
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
     * @return static
     */
    public function fill($newValue): self
    {
        foreach ($this->values as $index => $value) {
            $this->values[$index] = $newValue;
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function get($key)
    {
        if ($this->offsetExists($key) === false) {
            throw new OutOfBoundsException(sprintf(static::MSG_UNDEFINED_OFFSET, $key));
        }

        return $this->values[$key];
    }

    /**
     * @param mixed $key
     * @param int   $parameterIndex
     * @throws TypeError
     */
    private function handleInvalidKey($key, int $parameterIndex = 1): void
    {
        if (is_numeric($key) === false) {
            throw new TypeError(sprintf(self::MSG_ILLEGAL_ARGUMENT_TYPE, $parameterIndex, gettype($key)));
        }
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    /**
     * @inheritdoc
     */
    public function key(): ?int
    {
        $key = key($this->values);

        return $key === null ? $key : (int) $key;
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->values);
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        $this->push($value);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Pushes specified value into array
     *
     * @param mixed    $value
     * @param int|null $repeat
     * @return static
     * @throws InvalidArgumentException
     */
    public function push($value, int $repeat = null): self
    {
        if ($repeat !== null && $repeat <= 0) {
            throw new InvalidArgumentException(sprintf(static::MSG_NEGATIVE_ARGUMENT_NOT_ALLOWED, 2, 1));
        }

        for ($i = 0; $i < $repeat; $i++) {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * Pushes multiple values to array
     *
     * @param array $values
     * @return static
     * @throws InvalidArgumentException
     */
    public function pushAll(array $values): self
    {
        foreach ($values as $value) {
            $this->push($value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function put($key, $value)
    {
        $this->handleInvalidKey($key);

        $this->values[$key] = $value;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function putAll(MapInterface $map): self
    {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function remove($key)
    {
        if ($this->offsetExists($key) === false) {
            throw new OutOfBoundsException(sprintf(static::MSG_UNDEFINED_OFFSET, $key));
        }

        unset($this->values[$key]);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        reset($this->values);
    }

    /**
     * @inheritdoc
     */
    public function size(): int
    {
        return count($this->values);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * @inheritdoc
     */
    public function values(): array
    {
        return array_values($this->values);
    }
}

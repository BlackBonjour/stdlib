<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayIterator;
use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Lang\StdObject;
use TypeError;

use function array_key_exists;
use function array_slice;
use function count;
use function gettype;
use function in_array;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.06.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Sequence extends StdObject implements MapInterface
{
    private array $values = [];

    public function clear(): void
    {
        $this->values = [];
    }

    public function containsKey($key): bool
    {
        if (is_numeric($key) === false) {
            throw new TypeError(
                sprintf('Expected argument %d to be numeric, %s given!', 1, gettype($key))
            );
        }

        return array_key_exists($key, $this->values);
    }

    public function containsValue($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * Returns a new sequence from specified array.
     *
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $array): self
    {
        return (new self())->pushAll($array);
    }

    public function count(): int
    {
        return $this->size();
    }

    public function current()
    {
        return current($this->values);
    }

    /**
     * Fills array with specified value.
     *
     * @param mixed $newValue
     */
    public function fill($newValue): self
    {
        foreach ($this->values as $index => $value) {
            $this->values[$index] = $newValue;
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws OutOfBoundsException
     */
    public function get($key)
    {
        if ($this->offsetExists($key) === false) {
            throw new OutOfBoundsException(sprintf('Offset %d does not exist!', $key));
        }

        return $this->values[$key];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->values);
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    public function key(): ?int
    {
        return key($this->values);
    }

    public function next(): void
    {
        next($this->values);
    }

    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritDoc
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        $this->push($value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Pushes specified value into array.
     *
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function push($value, int $repeat = null): self
    {
        if ($repeat !== null && $repeat <= 0) {
            throw new InvalidArgumentException(sprintf('Argument %d must be %d or higher!', 2, 1));
        }

        for ($i = 0; $i < ($repeat ?? 1); $i++) {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * Pushes multiple values to array.
     *
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
     * @inheritDoc
     */
    public function put($key, $value)
    {
        if (is_numeric($key) === false) {
            throw new TypeError(
                sprintf('Expected argument %d to be numeric, %s given!', 1, gettype($key))
            );
        }

        $this->values[$key] = $value;

        return $this;
    }

    public function putAll(MapInterface $map): self
    {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    public function remove($key)
    {
        if (is_numeric($key) === false) {
            throw new TypeError(
                sprintf('Expected argument %d to be numeric, %s given!', 1, gettype($key))
            );
        }

        unset($this->values[$key]);
    }

    public function rewind(): void
    {
        reset($this->values);
    }

    public function size(): int
    {
        return count($this->values);
    }

    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): self
    {
        $this->values = array_slice($this->values, $offset, $length, $preserveKeys);

        return $this;
    }

    public function sort(callable $callable): self
    {
        usort($this->values, $callable);

        return $this;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function values(): array
    {
        return array_values($this->values);
    }
}

<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Lang\StdObject;
use TypeError;
use function array_key_exists;
use function array_slice;
use function in_array;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.06.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Sequence extends StdObject implements MapInterface
{
    /** @var array */
    private $values = [];

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->values = [];
    }

    /**
     * @inheritDoc
     */
    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @inheritDoc
     */
    public function containsValue($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * Returns a new sequence from specified array.
     *
     * @param array $array
     * @throws InvalidArgumentException
     */
    public static function createFromArray(array $array): self
    {
        return (new self)->pushAll($array);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->size();
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    /**
     * @inheritDoc
     */
    public function key(): ?int
    {
        return key($this->values);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        next($this->values);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
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
     * @param array $values
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

    /**
     * @inheritDoc
     */
    public function putAll(MapInterface $map): self
    {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        unset($this->values[$key]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        reset($this->values);
    }

    /**
     * @inheritDoc
     */
    public function size(): int
    {
        return count($this->values);
    }

    /**
     * @inheritDoc
     */
    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): self
    {
        $this->values = array_slice($this->values, $offset, $length, $preserveKeys);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sort(callable $callable): self
    {
        usort($this->values, $callable);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->key() !== null;
    }

    /**
     * @inheritDoc
     */
    public function values(): array
    {
        return array_values($this->values);
    }
}

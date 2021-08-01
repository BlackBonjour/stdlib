<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayIterator;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use TypeError;

use function array_key_exists;
use function array_slice;
use function count;
use function gettype;
use function in_array;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Map implements MapInterface
{
    private array $mapping = [];

    public function clear(): void
    {
        $this->mapping = [];
    }

    public function containsKey($key): bool
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        return array_key_exists($key, $this->mapping);
    }

    public function containsValue($value): bool
    {
        return in_array($value, $this->mapping, true);
    }

    /**
     * Returns a new map containing key-value mapping from specified array.
     */
    public static function createFromArray(array $array): static
    {
        $map = new static();

        foreach ($array as $key => $value) {
            $map->put($key, $value);
        }

        return $map;
    }

    public function count(): int
    {
        return $this->size();
    }

    public function current()
    {
        return current($this->mapping);
    }

    /**
     * @inheritDoc
     * @throws OutOfBoundsException
     */
    public function get($key)
    {
        if ($this->containsKey($key) === false) {
            throw new OutOfBoundsException(sprintf('Offset %s does not exist!', $key));
        }

        return $this->mapping[$key];
    }

    public function getIterator(): iterable
    {
        return new ArrayIterator($this->mapping);
    }

    public function isEmpty(): bool
    {
        return empty($this->mapping);
    }

    public function key(): ?string
    {
        $key = key($this->mapping);

        return $key === null ? null : (string) $key;
    }

    public function next(): void
    {
        next($this->mapping);
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

    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    public function put($key, $value): static
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        $this->mapping[$key] = $value;

        return $this;
    }

    public function putAll(MapInterface $map): static
    {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    public function remove($key): void
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        unset($this->mapping[$key]);
    }

    public function rewind(): void
    {
        reset($this->mapping);
    }

    public function size(): int
    {
        return count($this->mapping);
    }

    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): static
    {
        $this->mapping = array_slice($this->mapping, $offset, $length, $preserveKeys);

        return $this;
    }

    public function sort(callable $callable): static
    {
        uasort($this->mapping, $callable);

        return $this;
    }

    public function toArray(): array
    {
        return $this->mapping;
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function values(): array
    {
        return array_values($this->mapping);
    }
}

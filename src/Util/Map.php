<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use TypeError;
use function array_key_exists;
use function array_slice;
use function in_array;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Map implements MapInterface
{
    /** @var array */
    private $mapping = [];

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->mapping = [];
    }

    /**
     * @inheritDoc
     */
    public function containsKey($key): bool
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        return array_key_exists($key, $this->mapping);
    }

    /**
     * @inheritDoc
     */
    public function containsValue($value): bool
    {
        return in_array($value, $this->mapping, true);
    }

    /**
     * Returns a new map containing key-value mapping from specified array.
     *
     * @param array $array
     */
    public static function createFromArray(array $array): self
    {
        $map = new self;

        foreach ($array as $key => $value) {
            $map->put($key, $value);
        }

        return $map;
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

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        return empty($this->mapping);
    }

    /**
     * @inheritDoc
     */
    public function key(): ?string
    {
        $key = key($this->mapping);

        return $key === null ? null : (string) $key;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        next($this->mapping);
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
     */
    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    public function put($key, $value): self
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        $this->mapping[$key] = $value;

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
    public function remove($key): void
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf('Expected argument 1 to be scalar, %s given!', gettype($key)));
        }

        unset($this->mapping[$key]);
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        reset($this->mapping);
    }

    /**
     * @inheritDoc
     */
    public function size(): int
    {
        return count($this->mapping);
    }

    /**
     * @inheritDoc
     */
    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): self
    {
        $this->mapping = array_slice($this->mapping, $offset, $length, $preserveKeys);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sort(callable $callable): self
    {
        uasort($this->mapping, $callable);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->mapping;
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
        return array_values($this->mapping);
    }
}

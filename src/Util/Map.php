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
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->mapping = [];
    }

    /**
     * @inheritdoc
     */
    public function containsKey($key): bool
    {
        $this->handleInvalidKey($key);

        return array_key_exists($key, $this->mapping);
    }

    /**
     * @inheritdoc
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
        return current($this->mapping);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     */
    public function get($key)
    {
        if ($this->containsKey($key) === false) {
            throw new OutOfBoundsException(sprintf('Offset %s does not exist!', $key));
        }

        return $this->mapping[$key];
    }

    private function handleInvalidKey($key, int $parameterIndex = 1): void
    {
        if (is_scalar($key) === false) {
            throw new TypeError(
                sprintf('Expected argument %d to be numeric, %s given!', $parameterIndex, gettype($key))
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return empty($this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function key(): ?string
    {
        $key = key($this->mapping);

        return $key === null ? null : (string) $key;
    }

    /**
     * @inheritdoc
     */
    public function next(): void
    {
        next($this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritdoc
     */
    public function put($key, $value): self
    {
        $this->handleInvalidKey($key);

        $this->mapping[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
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
     */
    public function remove($key): void
    {
        $this->handleInvalidKey($key);

        unset($this->mapping[$key]);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        reset($this->mapping);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->mapping;
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
        return array_values($this->mapping);
    }
}

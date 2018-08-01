<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use TypeError;

/**
 * Map
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Map implements MapInterface
{
    protected const TYPE_ERROR_MSG = 'Expected key to be of type string, %s given!';

    /** @var array */
    protected $mapping = [];

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->mapping = [];
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function containsKey($key): bool
    {
        if (\is_string($key) === false) {
            throw new TypeError(sprintf(static::TYPE_ERROR_MSG, gettype($key)));
        }

        return array_key_exists($key, $this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function containsValue($value): bool
    {
        return \in_array($value, $this->mapping, true);
    }

    /**
     * Returns a new map containing key-value mapping from specified array
     *
     * @param array $array
     * @return static
     * @throws TypeError
     */
    public static function createFromArray(array $array): self
    {
        $map = new static;

        foreach ($array as $key => $value) {
            $map->put((string) $key, $value);
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
     * @throws TypeError
     */
    public function get($key)
    {
        if (\is_string($key) === false) {
            throw new TypeError(sprintf(static::TYPE_ERROR_MSG, gettype($key)));
        }

        return $this->mapping[$key] ?? null;
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
    public function next()
    {
        return next($this->mapping);
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetExists($offset): bool
    {
        return is_scalar($offset) ? $this->containsKey((string) $offset): false;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetGet($offset)
    {
        return is_scalar($offset) ? $this->get((string) $offset): null;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetSet($offset, $value): void
    {
        if (is_scalar($offset)) {
            $this->put((string) $offset, $value);
        }
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function offsetUnset($offset): void
    {
        if (is_scalar($offset)) {
            $this->remove((string) $offset);
        }
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function put($key, $value): self
    {
        if (\is_string($key) === false) {
            throw new TypeError(sprintf(static::TYPE_ERROR_MSG, gettype($key)));
        }

        $this->mapping[$key] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function putAll(MapInterface $map): self
    {
        foreach ($map as $key => $value) {
            $this->put((string) $key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws TypeError
     */
    public function remove($key): void
    {
        if (\is_string($key) === false) {
            throw new TypeError(sprintf(static::TYPE_ERROR_MSG, gettype($key)));
        }

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
     * @inheritdoc
     */
    public function valid(): bool
    {
        return key($this->mapping) !== null;
    }

    /**
     * @inheritdoc
     */
    public function values(): array
    {
        return array_values($this->mapping);
    }
}

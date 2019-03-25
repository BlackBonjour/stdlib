<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Map implements MapInterface
{
    private const MSG_ILLEGAL_ARGUMENT_TYPE = 'Expected argument %d to be numeric, %s given!';
    private const MSG_UNDEFINED_OFFSET      = 'Offset %s does not exist!';

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
     * @throws TypeError
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
     * Returns a new map containing key-value mapping from specified array
     *
     * @param array $array
     * @return static
     * @throws TypeError
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
     * @throws TypeError
     */
    public function get($key)
    {
        if ($this->containsKey($key) === false) {
            throw new OutOfBoundsException(sprintf(self::MSG_UNDEFINED_OFFSET, $key));
        }

        return $this->mapping[$key];
    }

    /**
     * @param mixed $key
     * @param int   $parameterIndex
     * @throws TypeError
     */
    private function handleInvalidKey($key, int $parameterIndex = 1): void
    {
        if (is_scalar($key) === false) {
            throw new TypeError(sprintf(self::MSG_ILLEGAL_ARGUMENT_TYPE, $parameterIndex, gettype($key)));
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
     * @throws TypeError
     */
    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
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
     * @inheritdoc
     * @throws TypeError
     */
    public function put($key, $value): self
    {
        $this->handleInvalidKey($key);

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
            $this->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function remove($key): void
    {
        if ($this->containsKey($key) === false) {
            throw new OutOfBoundsException(sprintf(static::MSG_UNDEFINED_OFFSET, $key));
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

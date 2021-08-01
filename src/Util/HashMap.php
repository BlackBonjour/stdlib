<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use JsonException;

use function array_key_exists;
use function array_slice;
use function count;
use function in_array;
use function is_array;
use function is_object;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     27.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class HashMap implements MapInterface
{
    private array $keys = [];
    private array $values = [];

    public function clear(): void
    {
        $this->values = [];
        $this->keys   = [];
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function containsKey($key): bool
    {
        return isset($this->keys[static::stringifyKey($key)]);
    }

    public function containsValue($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * @throws JsonException
     */
    public static function createFromArray(array $array): static
    {
        $hashMap = new static();

        foreach ($array as $key => $value) {
            $hashMap->put($key, $value);
        }

        return $hashMap;
    }

    public function count(): int
    {
        return $this->size();
    }

    public function current()
    {
        $key = key($this->keys);

        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws OutOfBoundsException
     */
    public function get($key)
    {
        if ($this->containsKey($key) === false) {
            throw new OutOfBoundsException(sprintf('Offset %s does not exist!', static::stringifyKey($key)));
        }

        return $this->values[static::stringifyKey($key)];
    }

    public function getIterator(): iterable
    {
        foreach ($this->keys as $index => $key) {
            yield $key => $this->values[$index];
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->keys);
    }

    public function key()
    {
        $key = current($this->keys);

        return $key !== false ? $key : null;
    }

    public function next()
    {
        $key = next($this->keys);

        if ($key === false) {
            return false;
        }

        return $this->values[key($this->keys)];
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function offsetSet($offset, $value): void
    {
        $this->put($offset, $value);
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function put($key, $value): static
    {
        $index = static::stringifyKey($key);

        if (isset($this->keys[$index]) === false) {
            $this->keys[$index] = $key;
        }

        $this->values[$index] = $value;

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function putAll(MapInterface $map): static
    {
        foreach ($map as $key => $value) {
            $this->put($key, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function remove($key): void
    {
        $index = static::stringifyKey($key);

        unset($this->keys[$index], $this->values[$index]);
    }

    public function rewind(): void
    {
        reset($this->keys);
    }

    public function size(): int
    {
        return count($this->keys);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): static
    {
        if ($preserveKeys === false) {
            throw new InvalidArgumentException('Slice is only allowed with preserving keys!');
        }

        $this->keys   = array_slice($this->keys, $offset, $length, true);
        $this->values = array_slice($this->values, $offset, $length, true);

        return $this;
    }

    public function sort(callable $callable, bool $keySort = false): static
    {
        $keys   = $this->keys;
        $values = $this->values;

        if ($keySort) {
            uasort($keys, $callable);
            $values = array_replace($keys, $values);
        } else {
            uasort($values, $callable);
            $keys = array_replace($values, $keys);
        }

        $this->keys   = $keys;
        $this->values = $values;

        return $this;
    }

    /**
     * Calculates string representing specified array key.
     *
     * @throws JsonException
     */
    private static function stringifyArrayKey(array $key): string
    {
        ksort($key);

        foreach ($key as &$value) {
            if (is_array($value)) {
                $value = static::stringifyArrayKey($value);
            } elseif (is_object($value)) {
                $value = static::stringifyKey($value);
            }
        }

        return json_encode($key, JSON_THROW_ON_ERROR);
    }

    /**
     * Calculates string representing specified key.
     *
     * @throws JsonException
     */
    private static function stringifyKey($key): string
    {
        if ($key === null || is_scalar($key)) {
            return (string) $key;
        }

        if (is_object($key)) {
            return spl_object_hash($key);
        }

        return static::stringifyArrayKey($key);
    }

    /**
     * @see HashMap::values()
     */
    public function toArray(): array
    {
        return $this->values();
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

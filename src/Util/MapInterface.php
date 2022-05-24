<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
interface MapInterface extends ArrayAccess, Countable, Iterator
{
    /**
     * Removes all the mapping from this map.
     */
    public function clear(): void;

    /**
     * Returns true if this map contains a mapping for the specified key.
     */
    public function containsKey($key): bool;

    /**
     * Returns true if this map does contain at least one map to the specified value.
     */
    public function containsValue($value): bool;

    /**
     * Returns the value to which the specified key is mapped, or null if this map contains no mapping for the key.
     */
    public function get($key);

    /**
     * Returns true if this map contains no key-value mappings.
     */
    public function isEmpty(): bool;

    /**
     * Associates the specified value with the specified key in this map.
     */
    public function put($key, $value): static;

    /**
     * Copies all the mapping from the specified map to this map.
     */
    public function putAll(MapInterface $map): static;

    /**
     * Removes the mapping for a key from this map if it is present.
     */
    public function remove($key): void;

    /**
     * Returns the number of key-value mappings in this map.
     */
    public function size(): int;

    public function slice(int $length, int $offset = 0, bool $preserveKeys = true): static;

    /**
     * Sorts this hash map using retrieved callback.
     */
    public function sort(callable $callback): static;

    public function toArray(): array;

    /**
     * Returns an array with all values contained in this map.
     */
    public function values(): array;
}

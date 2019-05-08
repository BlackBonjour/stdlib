<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
interface MapInterface extends ArrayAccess, Countable, Iterator
{
    /**
     * Removes all of the mapping from this map
     */
    public function clear(): void;

    /**
     * Returns true if this map contains a mapping for the specified key
     *
     * @param mixed $key
     * @return boolean
     */
    public function containsKey($key): bool;

    /**
     * Returns true if this map maps one or more keys to the specified value
     *
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value): bool;

    /**
     * Returns the value to which the specified key is mapped, or null if this
     * map contains no mapping for the key
     *
     * @param mixed $key
     * @return mixed
     */
    public function get($key);

    /**
     * Returns true if this map contains no key-value mappings
     *
     * @return boolean
     */
    public function isEmpty(): bool;

    /**
     * Associates the specified value with the specified key in this map
     *
     * @param mixed $key
     * @param mixed  $value
     * @return static
     */
    public function put($key, $value);

    /**
     * Copies all of the mapping from the specified map to this map
     *
     * @param MapInterface $map
     * @return static
     */
    public function putAll(MapInterface $map);

    /**
     * Removes the mapping for a key from this map if it is present
     *
     * @param mixed $key
     * @return mixed
     */
    public function remove($key);

    /**
     * Returns the number of key-value mappings in this map
     *
     * @return int
     */
    public function size(): int;

    /**
     * @param int  $length
     * @param int  $offset
     * @param bool $preserveKeys
     * @return static
     */
    public function slice(int $length, int $offset = 0, bool $preserveKeys = true);

    /**
     * Sorts this hash map using retrieved callback.
     *
     * @param callable $callable
     * @return static
     */
    public function sort(callable $callable);

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * Returns an array with all values contained in this map
     *
     * @return array
     */
    public function values(): array;
}

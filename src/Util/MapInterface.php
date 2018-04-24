<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Map interface
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       24.04.2018
 * @package     BlackBonjour\Stdlib\Util
 * @copyright   Copyright (c) 2018 Erick Dyck
 */
interface MapInterface extends ArrayAccess, Countable, Iterator
{
    /**
     * Removes all of the mapping from this map
     */
    public function clear() : void;

    /**
     * Returns true if this map contains a mapping for the specified key
     *
     * @param string $key
     * @return boolean
     */
    public function containsKey(string $key) : bool;

    /**
     * Returns true if this map maps one or more keys to the specified value
     *
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value) : bool;

    /**
     * Returns the value to which the specified key is mapped, or null if this
     * map contains no mapping for the key
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Returns true if this map contains no key-value mappings
     *
     * @return boolean
     */
    public function isEmpty() : bool;

    /**
     * Associates the specified value with the specified key in this map
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function put(string $key, $value);

    /**
     * Copies all of the mapping from the specified map to this map
     *
     * @param MapInterface $map
     * @return $this
     */
    public function putAll(MapInterface $map);

    /**
     * Removes the mapping for a key from this map if it is present
     *
     * @param string $key
     * @return mixed
     */
    public function remove(string $key);

    /**
     * Returns the number of key-value mappings in this map
     *
     * @return int
     */
    public function size() : int;

    /**
     * Returns an array with all values contained in this map
     *
     * @return array
     */
    public function values() : array;
}

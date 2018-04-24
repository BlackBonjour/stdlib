<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

/**
 * Map
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       24.04.2018
 * @package     BlackBonjour\Stdlib\Util
 * @copyright   Copyright (c) 2018 Erick Dyck
 */
class Map implements MapInterface
{
    /** @var array */
    protected $mapping = [];

    /**
     * @inheritdoc
     */
    public function clear() : void
    {
        $this->mapping = [];
    }

    /**
     * @inheritdoc
     */
    public function containsKey(string $key) : bool
    {
        return array_key_exists($key, $this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function containsValue($value) : bool
    {
        return \in_array($value, $this->mapping, true);
    }

    /**
     * Returns a new map containing key-value mapping from specified array
     *
     * @param array $array
     * @return $this
     */
    public static function createFromArray(array $array) : self
    {
        $map = new self;

        foreach ($array as $key => $value) {
            $map->put((string) $key, $value);
        }

        return $map;
    }

    /**
     * @inheritdoc
     */
    public function count() : int
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
     */
    public function get(string $key)
    {
        return $this->mapping[$key] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty() : bool
    {
        return empty($this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function key() : ?string
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
     */
    public function offsetExists($offset) : bool
    {
        return is_scalar($offset) ? $this->containsKey((string) $offset) : false;
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return is_scalar($offset) ? $this->get((string) $offset) : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_scalar($offset)) {
            $this->put((string) $offset, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset) : void
    {
        if (is_scalar($offset)) {
            $this->remove((string) $offset);
        }
    }

    /**
     * @inheritdoc
     */
    public function put(string $key, $value) : self
    {
        $this->mapping[$key] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function putAll(MapInterface $map) : self
    {
        foreach ($map as $key => $value) {
            $this->put((string) $key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function remove(string $key) : void
    {
        unset($this->mapping[$key]);
    }

    /**
     * @inheritdoc
     */
    public function rewind() : void
    {
        reset($this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function size() : int
    {
        return count($this->mapping);
    }

    /**
     * @inheritdoc
     */
    public function valid() : bool
    {
        return $this->key() !== null;
    }

    /**
     * @inheritdoc
     */
    public function values() : array
    {
        return array_values($this->mapping);
    }
}

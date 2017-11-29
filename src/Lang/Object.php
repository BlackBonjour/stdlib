<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

/**
 * Root class of all objects
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       22.11.2017
 * @package     BlackBonjour\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 */
class Object
{
    const DEFAULT_VALUE = null;

    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function __toString() : string
    {
        return \get_class($this) . '@' . $this->hashCode();
    }

    /**
     * Returns a copy of this object
     *
     * @return  $this
     */
    public function clone()
    {
        return clone $this;
    }

    /**
     * Checks if given object is equal to this one
     *
     * @param   mixed   $obj
     * @return  boolean
     */
    public function equals($obj) : bool
    {
        // We ain't gonna check for same reference here!
        return $this == $obj;
    }

    /**
     * Returns a hash code value for this object
     *
     * @return  string
     */
    public function hashCode() : string
    {
        return spl_object_hash($this);
    }
}

<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use function get_class;

/**
 * Root class of all objects.
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     22.11.2017
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class StdObject
{
    /**
     * Returns a string representation of this object.
     */
    public function __toString(): string
    {
        return get_class($this) . '@' . $this->hashCode();
    }

    /**
     * Returns a copy of this object.
     *
     * @return static
     */
    public function clone(): self
    {
        return clone $this;
    }

    /**
     * Checks if given object is equal to this one.
     *
     * @param mixed $obj
     */
    public function equals($obj): bool
    {
        return $this == $obj;
    }

    /**
     * Returns a hash code value for this object.
     */
    public function hashCode(): string
    {
        return spl_object_hash($this);
    }
}

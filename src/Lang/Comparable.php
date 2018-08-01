<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

/**
 * Imposes a total ordering on the objects of each class that implements it
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     30.11.2017
 * @package   BlackBonjour\Stdlib\Lang
 * @copyright Copyright (c) 2017 Erick Dyck
 */
interface Comparable
{
    /**
     * Compares given object with current one
     *
     * @param mixed $object
     * @return int
     */
    public function compareTo($object): int;
}

<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\OutOfBoundsException;

/**
 * Interface for readable sequences of char values.
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     05.12.2017
 * @package   BlackBonjour\Stdlib
 * @copyright Copyright (c) 2017 Erick Dyck
 */
interface CharSequence
{
    /**
     * Returns a string containing the characters in this sequence.
     */
    public function __toString(): string;

    /**
     * Returns the character at specified index.
     */
    public function charAt(int $index): Character;

    /**
     * Returns the length of this character sequence.
     */
    public function length(): int;

    /**
     * Returns an array containing characters between specified start index and
     * end index.
     *
     * @return Character[]
     * @throws OutOfBoundsException
     */
    public function subSequence(int $begin, int $end): array;
}

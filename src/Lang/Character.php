<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Lang;

use InvalidArgumentException;

/**
 * Represents a single character
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       04.12.2017
 * @package     BlackBonjour\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 */
class Character extends Object implements Comparable
{
    const DEFAULT_VALUE = '';

    /** @var string */
    protected $data = self::DEFAULT_VALUE;

    /**
     * Constructor
     *
     * @param   string  $char
     * @throws  InvalidArgumentException
     */
    public function __construct(string $char)
    {
        if (mb_strlen($char) > 1) {
            throw new InvalidArgumentException('Only one character can be represented!');
        }

        $this->data = $char;
    }

    /**
     * This strings value
     *
     * @return  string
     */
    public function __toString() : string
    {
        return $this->data;
    }

    /**
     * Compares given character with this character
     *
     * @param   Character|string    $char
     * @return  int
     * @throws  InvalidArgumentException
     */
    public function compareTo($char) : int
    {
        self::handleIncomingChar($char);
        return strcmp($this->data, (string) $char) <=> 0;
    }

    /**
     * Validates given character and throws an exception if required
     *
     * @param   mixed[] $chars
     * @return  void
     * @throws  InvalidArgumentException
     */
    private static function handleIncomingChar(...$chars)
    {
        foreach ($chars as $char) {
            if (self::validateChar($char) === false) {
                throw new InvalidArgumentException('Given value must be of type string or a character related object!');
            }
        }
    }

    /**
     * Validates given character
     *
     * @param   Character|string    $char
     * @return  boolean
     */
    private static function validateChar($char) : bool
    {
        return (\is_string($char) && mb_strlen($char) === 1) || $char instanceof self;
    }
}

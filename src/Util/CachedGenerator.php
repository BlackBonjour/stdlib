<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use Generator;
use IteratorAggregate;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     13.11.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class CachedGenerator implements IteratorAggregate
{
    /** @var Generator */
    private $generator;

    /** @var array */
    private $items = [];

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): Generator
    {
        yield from $this->items;

        while ($this->generator->valid()) {
            $key               = $this->generator->key();
            $this->items[$key] = $value = $this->generator->current();

            yield $key => $value;

            $this->generator->next();
        }
    }
}

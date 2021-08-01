<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use Generator;
use IteratorAggregate;

use function array_key_exists;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     13.11.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class CachedGenerator implements IteratorAggregate
{
    private array $keys = [];
    private array $values = [];

    public function __construct(
        private Generator $generator,
    ) {
    }

    public function getIterator(): Generator
    {
        foreach ($this->keys as $index => $key) {
            if (array_key_exists($index, $this->values) === false) {
                throw new OutOfBoundsException('Cache index mismatch!');
            }

            yield $key => $this->values[$index];
        }

        while ($this->generator->valid()) {
            $key   = $this->generator->key();
            $value = $this->generator->current();

            $this->keys[]   = $key;
            $this->values[] = $value;

            yield $key => $value;

            $this->generator->next();
        }
    }
}

<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpMissingDocCommentInspection */
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\CachedGenerator;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     13.11.2018
 * @package   BlackBonjourTest\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 * @covers    \BlackBonjour\Stdlib\Util\CachedGenerator
 */
class CachedGeneratorTest extends TestCase
{
    private $iterations = 0;

    private function getIterator(): Generator
    {
        foreach (['foo', 'bar', 'baz'] as $value) {
            yield $value;

            ++$this->iterations;
        }
    }

    public function testGetIterator(): void
    {
        $expectation     = $this->getIterator();
        $cachedGenerator = new CachedGenerator($expectation);

        self::assertEquals($expectation, $cachedGenerator->getIterator());

        // Test caching
        $i = 0;

        foreach ($cachedGenerator->getIterator() as $value) {
            if ($i === 2) {
                break;
            }

            ++$i;
        }

        self::assertEquals(2, $this->iterations);

        foreach ($cachedGenerator->getIterator() as $value) {
            // ...
        }

        self::assertEquals(3, $this->iterations);
    }
}

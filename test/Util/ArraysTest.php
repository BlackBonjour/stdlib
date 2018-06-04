<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\Arrays;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for arrays
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     04.06.2018
 * @package   BlackBonjourTest\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class ArraysTest extends TestCase
{
    public function testArrayAccess() : void
    {
        $arrays = new Arrays(['Foo', 'Bar', 'Baz']);

        self::assertTrue(isset($arrays[2]));
        self::assertEquals('Bar', $arrays[1]);

        unset($arrays[2]);
        self::assertCount(2, $arrays);

        $arrays[2] = 'Lorem Ipsum';
        self::assertCount(3, $arrays);
    }

    public function testFill() : void
    {
        $array = new Arrays(['Foo', 'Bar', 'Baz']);
        $array->fill('Lorem Ipsum');

        self::assertEquals(array_fill(0, 3, 'Lorem Ipsum'), $array->toArray());
    }

    public function testPush() : void
    {
        $array = new Arrays;
        $array->push('FooBar');

        self::assertEquals('FooBar', $array->current());

        $array->push('Lorem Ipsum', 3);
        self::assertEquals(['FooBar', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum'], $array->toArray());
    }

    public function testPushAll() : void
    {
        $array = new Arrays;
        $array->pushAll(['Foo', 'Bar', 'Baz']);

        self::assertEquals(['Foo', 'Bar', 'Baz'], $array->toArray());
    }
}

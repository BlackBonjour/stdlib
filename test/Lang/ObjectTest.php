<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\Object;
use PHPUnit\Framework\TestCase;

/**
 * Test for Object class
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       29.11.2017
 * @package     BlackBonjourTest\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 * @covers      \BlackBonjour\Stdlib\Lang\Object
 */
class ObjectTest extends TestCase
{
    public function test__toString()
    {
        $obj = new Object;
        self::assertEquals(Object::class . '@' . spl_object_hash($obj), (string) $obj);
    }

    public function testClone()
    {
        $obj = new Object;
        self::assertInstanceOf(Object::class, $obj->clone());
    }

    public function testEquals()
    {
        $objA = new Object;
        $objB = new Object;
        $objC = new \stdClass;

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
    }

    public function testHashCode()
    {
        $obj = new Object;
        self::assertEquals(spl_object_hash($obj), $obj->hashCode());
    }
}

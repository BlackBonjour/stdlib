<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\StdObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Object class
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       29.11.2017
 * @package     BlackBonjourTest\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 * @covers      \BlackBonjour\Stdlib\Lang\StdObject
 */
class StdObjectTest extends TestCase
{
    public function test__toString()
    {
        $obj = new StdObject;
        self::assertEquals(StdObject::class . '@' . spl_object_hash($obj), (string) $obj);
    }

    public function testClone()
    {
        $obj = new StdObject;
        self::assertInstanceOf(StdObject::class, $obj->clone());
    }

    public function testEquals()
    {
        $objA = new StdObject;
        $objB = new StdObject;
        $objC = new \stdClass;

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
    }

    public function testHashCode()
    {
        $obj = new StdObject;
        self::assertEquals(spl_object_hash($obj), $obj->hashCode());
    }
}

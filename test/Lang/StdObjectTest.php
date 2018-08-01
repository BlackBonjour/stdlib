<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\StdObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Test for Object class
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     29.11.2017
 * @package   BlackBonjourTest\Stdlib\Lang
 * @copyright Copyright (c) 2017 Erick Dyck
 * @covers    \BlackBonjour\Stdlib\Lang\StdObject
 */
class StdObjectTest extends TestCase
{
    public function test__toString(): void
    {
        $obj = new StdObject;
        self::assertEquals(StdObject::class . '@' . spl_object_hash($obj), (string) $obj);
    }

    public function testClone(): void
    {
        $obj = new StdObject;
        self::assertInstanceOf(StdObject::class, $obj->clone());
    }

    public function testEquals(): void
    {
        $objA = new StdObject;
        $objB = new StdObject;
        $objC = new stdClass;

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
    }

    public function testHashCode(): void
    {
        $obj = new StdObject;
        self::assertEquals(spl_object_hash($obj), $obj->hashCode());
    }
}

<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\Object;
use BlackBonjour\Stdlib\Lang\StdString;
use PHPUnit\Framework\TestCase;

/**
 * Test for StdString class
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       29.11.2017
 * @package     BlackBonjourTest\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 * @covers      \BlackBonjour\Stdlib\Lang\StdString
 */
class StdClassTest extends TestCase
{
    private function getObject(string $string = 'FooBar') : StdString
    {
        return new StdString($string);
    }

    public function test__toString()
    {
        $string = $this->getObject();
        self::assertEquals('FooBar', (string) $string);
    }

    public function testCharAt()
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        self::assertEquals('B', $stringA->charAt(3));
        self::assertEquals('с', $stringB->charAt(2));
    }

    public function testClone()
    {
        $string = $this->getObject();
        self::assertInstanceOf(StdString::class, $string->clone());
    }

    public function testCompareTo()
    {
        $string = $this->getObject();

        self::assertEquals(-1, $string->compareTo('Тест'));
        self::assertEquals(-1, $string->compareTo('foobar'));

        self::assertEquals(0, $string->compareTo('FooBar'));
        self::assertEquals(0, $string->compareTo($this->getObject()));

        self::assertEquals(1, $string->compareTo('Alpha'));
        self::assertEquals(1, $string->compareTo('Babushka'));
    }

    public function testCompareToIgnoreCase()
    {
        $string = $this->getObject();

        self::assertEquals(-1, $string->compareToIgnoreCase('тест'));
        self::assertEquals(-1, $string->compareToIgnoreCase($this->getObject('тест')));

        self::assertEquals(0, $string->compareToIgnoreCase('foobar'));
        self::assertEquals(0, $string->compareToIgnoreCase($this->getObject('foobar')));

        self::assertEquals(1, $string->compareToIgnoreCase('alpha'));
        self::assertEquals(1, $string->compareToIgnoreCase($this->getObject('alpha')));
    }

    public function testConcat()
    {
        $string = $this->getObject();

        self::assertEquals('FooBarTest', (string) (clone $string)->concat('Test'));
        self::assertEquals('FooBarTest', (string) (clone $string)->concat($this->getObject('Test')));
        self::assertEquals('FooBarТест', (string) (clone $string)->concat('Тест'));
    }

    public function testContains()
    {
        $string = $this->getObject();

        self::assertTrue($string->contains('oo')); // Latin
        self::assertFalse($string->contains('оо')); // Cyrillic
    }

    public function testContentEquals()
    {
        $string = $this->getObject();

        self::assertTrue($string->contentEquals('FooBar'));
        self::assertTrue($string->contentEquals($this->getObject()));

        self::assertFalse($string->contentEquals('foobar'));
        self::assertFalse($string->contentEquals('Тест'));
    }

    public function testEquals()
    {
        $objA = $this->getObject();
        $objB = $this->getObject();
        $objC = $this->getObject('Dis I Like');
        $objD = new Object;

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
        self::assertFalse($objA->equals($objD));
    }

    public function testHashCode()
    {
        $string = $this->getObject();
        self::assertEquals(spl_object_hash($string), $string->hashCode());
    }
}

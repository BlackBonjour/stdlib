<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\Character;
use BlackBonjour\Stdlib\Lang\CharSequence;
use BlackBonjour\Stdlib\Lang\StdString;
use PHPUnit\Framework\TestCase;

/**
 * Test for StdString class
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       05.12.2017
 * @package     BlackBonjourTest\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 * @covers      \BlackBonjour\Stdlib\Lang\Character
 */
class CharacterTest extends TestCase
{
    public function dataProviderCodePointAt() : array
    {
        $charA = $this->getObject('a');
        $charB = $this->getObject('B');
        $charF = $this->getObject('F');
        $charO = $this->getObject('o');
        $charR = $this->getObject('r');

        return [
            [97, [$charF, $charO, $charO, $charB, $charA, $charR], 4],
            [97, new StdString('FooBar'), 4],
        ];
    }

    public function dataProviderCodePointBefore() : array
    {
        $charA = $this->getObject('a');
        $charB = $this->getObject('B');
        $charF = $this->getObject('F');
        $charO = $this->getObject('o');
        $charR = $this->getObject('r');

        return [
            [97, [$charF, $charO, $charO, $charB, $charA, $charR], 5],
            [97, new StdString('FooBar'), 5],
        ];
    }

    public function dataProviderCompareTo() : array
    {
        $charA = $this->getObject();    // Latin
        $charB = $this->getObject('в'); // Cyrillic

        return [
            // Latin test
            [$charA, 'c', 0],
            [$charA, $this->getObject(), 0],
            [$charA, 'd', -1],
            [$charA, 'b', 1],

            // Cyrillic test
            [$charB, 'в', 0],
            [$charB, $this->getObject('в'), 0],
            [$charB, 'г', -1],
            [$charB, 'б', 1],
        ];
    }

    private function getObject(string $char = 'c') : Character
    {
        return new Character($char);
    }

    public function test__toString()
    {
        self::assertEquals('c', (string) $this->getObject()); // Latin
        self::assertEquals('с', (string) $this->getObject('с')); // Cyrillic
    }

    public function testCharCount()
    {
        self::assertEquals(1, Character::charCount('c'));
        self::assertEquals(1, Character::charCount($this->getObject()));
        self::assertEquals(1, Character::charCount('я'));
        self::assertEquals(2, Character::charCount('😁'));
    }

    public function testClone()
    {
        $char = $this->getObject();
        self::assertInstanceOf(Character::class, $char->clone());
    }



    /**
     * @param   int                 $expected
     * @param   CharSequence|array  $chars
     * @param   int                 $index
     * @param   int                 $limit
     * @dataProvider    dataProviderCodePointAt
     */
    public function testCodePointAt(int $expected, $chars, int $index, int $limit = null)
    {
        self::assertEquals($expected, Character::codePointAt($chars, $index, $limit));
    }

    /**
     * @param   int                 $expected
     * @param   CharSequence|array  $chars
     * @param   int                 $index
     * @param   int                 $start
     * @dataProvider    dataProviderCodePointBefore
     */
    public function testCodePointBefore(int $expected, $chars, int $index, int $start = null)
    {
        self::assertEquals($expected, Character::codePointBefore($chars, $index, $start));
    }

    /**
     * @param   Character           $char
     * @param   Character|string    $compare
     * @param   int                 $expected
     * @dataProvider    dataProviderCompareTo
     */
    public function testCompareTo(Character $char, $compare, int $expected)
    {
        self::assertEquals($expected, $char->compareTo($compare));
    }

    public function testEquals()
    {
        // Latin base character
        self::assertTrue($this->getObject()->equals('c'));
        self::assertTrue($this->getObject()->equals($this->getObject()));
        self::assertFalse($this->getObject()->equals('с')); // Cyrillic

        // Cyrillic base character
        self::assertTrue($this->getObject('с')->equals('с'));
        self::assertTrue($this->getObject('с')->equals($this->getObject('с')));
        self::assertFalse($this->getObject('с')->equals('c')); // Latin
    }

    public function testHashCode()
    {
        $charA = $this->getObject();
        $charB = $this->getObject('с');

        self::assertEquals(spl_object_hash($charA), $charA->hashCode());
        self::assertEquals(spl_object_hash($charB), $charB->hashCode());
    }
}

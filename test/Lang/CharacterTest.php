<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Lang\Character;
use BlackBonjour\Stdlib\Lang\CharSequence;
use BlackBonjour\Stdlib\Lang\StdString;
use InvalidArgumentException;
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

    public function dataProviderToLowerCase() : array
    {
        $charA = $this->getObject(); // Latin
        $charB = $this->getObject('б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'C'],
            'latin-char'      => [$charA, $this->getObject('C')],
            'cyrillic-string' => [$charB, 'Б'],
            'cyrillic-char'   => [$charB, $this->getObject('Б')],
        ];
    }

    public function dataProviderToUpperCase() : array
    {
        $charA = $this->getObject('C'); // Latin
        $charB = $this->getObject('Б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'c'],
            'latin-char'      => [$charA, $this->getObject()],
            'cyrillic-string' => [$charB, 'б'],
            'cyrillic-char'   => [$charB, $this->getObject('б')],
        ];
    }

    public function dataProviderValueOf() : array
    {
        $charA = $this->getObject(); // Latin
        $charB = $this->getObject('б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'c'],
            'latin-char'      => [$charA, $this->getObject()],
            'cyrillic-string' => [$charB, 'б'],
            'cyrillic-char'   => [$charB, $this->getObject('б')],
            'exception'       => [null, null, true, InvalidArgumentException::class],
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

    public function testIsLowerCase()
    {
        self::assertTrue(Character::isLowerCase($this->getObject())); // Latin
        self::assertTrue(Character::isLowerCase($this->getObject('с'))); // Cyrillic
        self::assertTrue(Character::isLowerCase('c'));
        self::assertFalse(Character::isLowerCase($this->getObject('C'))); // Latin
        self::assertFalse(Character::isLowerCase($this->getObject('С'))); // Cyrillic
        self::assertFalse(Character::isLowerCase('C'));
    }

    public function testIsUpperCase()
    {
        self::assertTrue(Character::isUpperCase($this->getObject('C'))); // Latin
        self::assertTrue(Character::isUpperCase($this->getObject('С'))); // Cyrillic
        self::assertTrue(Character::isUpperCase('C'));
        self::assertFalse(Character::isUpperCase($this->getObject())); // Latin
        self::assertFalse(Character::isUpperCase($this->getObject('с'))); // Cyrillic
        self::assertFalse(Character::isUpperCase('c'));
    }

    /**
     * @param   Character           $expectation
     * @param   Character|string    $char
     * @dataProvider    dataProviderToLowerCase
     */
    public function testToLowerCase(Character $expectation, $char)
    {
        self::assertEquals($expectation, Character::toLowerCase($char));
    }

    /**
     * @param   Character           $expectation
     * @param   Character|string    $char
     * @dataProvider    dataProviderToUpperCase
     */
    public function testToUpperCase(Character $expectation, $char)
    {
        self::assertEquals($expectation, Character::toUpperCase($char));
    }

    /**
     * @param   Character   $expectation
     * @param   mixed       $char
     * @param   boolean     $expectException
     * @param   string      $exception
     * @dataProvider    dataProviderValueOf
     */
    public function testValueOf($expectation, $char, bool $expectException = false, string $exception = null) {
        if ($expectException) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, Character::valueOf($char));
    }
}

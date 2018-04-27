<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Lang\Character;
use BlackBonjour\Stdlib\Lang\CharSequence;
use BlackBonjour\Stdlib\Lang\StdString;
use PHPUnit\Framework\TestCase;
use TypeError;

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
            'char-array'         => [97, [$charF, $charO, $charO, $charB, $charA, $charR], 4],
            'char-sequence'      => [97, new StdString('FooBar'), 4],
            'with-limit'         => [97, new StdString('FooBar'), 4, 5],
            'invalid-char'       => [0, 666, 2, null, InvalidArgumentException::class],
            'empty-char-array'   => [0, [], 2, null, InvalidArgumentException::class],
            'invalid-char-array' => [0, [666, 333, 223], 2, null, InvalidArgumentException::class],
            'invalid-index'      => [0, new StdString('topkek'), -1, null, InvalidArgumentException::class],
            'limit-below-index'  => [97, new StdString('FooBar'), 4, 3, InvalidArgumentException::class],
            'invalid-limit'      => [97, new StdString('FooBar'), 4, -1, InvalidArgumentException::class],
            'limit-above-length' => [97, new StdString('FooBar'), 4, 7, InvalidArgumentException::class],
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
            'char-array'         => [97, [$charF, $charO, $charO, $charB, $charA, $charR], 5],
            'char-sequence'      => [97, new StdString('FooBar'), 5],
            'with-start'         => [97, new StdString('FooBar'), 5, 1],
            'invalid-start'      => [97, new StdString('FooBar'), 5, -1, InvalidArgumentException::class],
            'index-below-start'  => [97, new StdString('FooBar'), 2, 3, InvalidArgumentException::class],
            'index-equals-start' => [97, new StdString('FooBar'), 2, 3, InvalidArgumentException::class],
        ];
    }

    public function dataProviderCompareTo() : array
    {
        $charA = $this->getObject();    // Latin
        $charB = $this->getObject('в'); // Cyrillic

        return [
            // Latin test
            'latin-string' => [$charA, 'c', 0],
            'latin-char'   => [$charA, $this->getObject(), 0],
            'latin-higher' => [$charA, 'd', -1],
            'latin-lower'  => [$charA, 'b', 1],

            // Cyrillic test
            'cyrillic-string' => [$charB, 'в', 0],
            'cyrillic-char'   => [$charB, $this->getObject('в'), 0],
            'cyrillic-higher' => [$charB, 'г', -1],
            'cyrillic-lower'  => [$charB, 'б', 1],
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

    public function test__construct() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getObject('cc');
    }

    public function test__toString() : void
    {
        self::assertEquals('c', (string) $this->getObject()); // Latin
        self::assertEquals('с', (string) $this->getObject('с')); // Cyrillic
    }

    /**
     * @throws TypeError
     */
    public function testCharCount() : void
    {
        self::assertEquals(1, Character::charCount('c'));
        self::assertEquals(1, Character::charCount($this->getObject()));
        self::assertEquals(1, Character::charCount('я'));
        self::assertEquals(2, Character::charCount('😁'));
    }

    public function testClone() : void
    {
        $char = $this->getObject();
        self::assertInstanceOf(Character::class, $char->clone());
    }

    /**
     * @param int                $expected
     * @param CharSequence|array $chars
     * @param int                $index
     * @param int                $limit
     * @param string             $exception
     * @dataProvider dataProviderCodePointAt
     */
    public function testCodePointAt(
        int $expected,
        $chars,
        int $index,
        int $limit = null,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, Character::codePointAt($chars, $index, $limit));
    }

    /**
     * @param int                $expected
     * @param CharSequence|array $chars
     * @param int                $index
     * @param int                $start
     * @param string             $exception
     * @dataProvider dataProviderCodePointBefore
     */
    public function testCodePointBefore(
        int $expected,
        $chars,
        int $index,
        int $start = null,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, Character::codePointBefore($chars, $index, $start));
    }

    /**
     * @param Character        $char
     * @param Character|string $compare
     * @param int              $expected
     * @throws TypeError
     * @dataProvider dataProviderCompareTo
     */
    public function testCompareTo(Character $char, $compare, int $expected) : void
    {
        self::assertEquals($expected, $char->compareTo($compare));
    }

    public function testEquals() : void
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

    public function testHashCode() : void
    {
        $charA = $this->getObject();
        $charB = $this->getObject('с');

        self::assertEquals(spl_object_hash($charA), $charA->hashCode());
        self::assertEquals(spl_object_hash($charB), $charB->hashCode());
    }

    /**
     * @throws TypeError
     */
    public function testIsLowerCase() : void
    {
        self::assertTrue(Character::isLowerCase($this->getObject())); // Latin
        self::assertTrue(Character::isLowerCase($this->getObject('с'))); // Cyrillic
        self::assertTrue(Character::isLowerCase('c'));
        self::assertFalse(Character::isLowerCase($this->getObject('C'))); // Latin
        self::assertFalse(Character::isLowerCase($this->getObject('С'))); // Cyrillic
        self::assertFalse(Character::isLowerCase('C'));
    }

    /**
     * @throws TypeError
     */
    public function testIsUpperCase() : void
    {
        self::assertTrue(Character::isUpperCase($this->getObject('C'))); // Latin
        self::assertTrue(Character::isUpperCase($this->getObject('С'))); // Cyrillic
        self::assertTrue(Character::isUpperCase('C'));
        self::assertFalse(Character::isUpperCase($this->getObject())); // Latin
        self::assertFalse(Character::isUpperCase($this->getObject('с'))); // Cyrillic
        self::assertFalse(Character::isUpperCase('c'));
    }

    /**
     * @param Character        $expectation
     * @param Character|string $char
     * @throws TypeError
     * @dataProvider dataProviderToLowerCase
     */
    public function testToLowerCase(Character $expectation, $char) : void
    {
        self::assertEquals($expectation, Character::toLowerCase($char));
    }

    /**
     * @param Character        $expectation
     * @param Character|string $char
     * @throws TypeError
     * @dataProvider dataProviderToUpperCase
     */
    public function testToUpperCase(Character $expectation, $char) : void
    {
        self::assertEquals($expectation, Character::toUpperCase($char));
    }

    /**
     * @param Character $expectation
     * @param mixed     $char
     * @param boolean   $expectException
     * @param string    $exception
     * @dataProvider dataProviderValueOf
     */
    public function testValueOf($expectation, $char, bool $expectException = false, string $exception = null) : void
    {
        if ($expectException) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, Character::valueOf($char));
    }
}

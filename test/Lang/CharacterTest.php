<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Lang\Character;
use BlackBonjour\Stdlib\Lang\CharSequence;
use BlackBonjour\Stdlib\Lang\StdString;
use PHPUnit\Framework\TestCase;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     05.12.2017
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class CharacterTest extends TestCase
{
    public function dataProviderCodePointAt(): array
    {
        $charA = new Character('a');
        $charB = new Character('B');
        $charF = new Character('F');
        $charO = new Character('o');
        $charR = new Character('r');

        return [
            'char-array'         => [97, [$charF, $charO, $charO, $charB, $charA, $charR], 4],
            'char-sequence'      => [97, new StdString('FooBar'), 4],
            'with-limit'         => [97, new StdString('FooBar'), 4, 5],
            'empty-char-array'   => [0, [], 2, null, InvalidArgumentException::class],
            'invalid-char-array' => [0, [666, 333, 223], 2, null, InvalidArgumentException::class],
            'invalid-index'      => [0, new StdString('topkek'), -1, null, InvalidArgumentException::class],
            'limit-below-index'  => [97, new StdString('FooBar'), 4, 3, InvalidArgumentException::class],
            'invalid-limit'      => [97, new StdString('FooBar'), 4, -1, InvalidArgumentException::class],
            'limit-above-length' => [97, new StdString('FooBar'), 4, 7, InvalidArgumentException::class],
        ];
    }

    public function dataProviderCodePointBefore(): array
    {
        $charA = new Character('a');
        $charB = new Character('B');
        $charF = new Character('F');
        $charO = new Character('o');
        $charR = new Character('r');

        return [
            'char-array'         => [97, [$charF, $charO, $charO, $charB, $charA, $charR], 5],
            'char-sequence'      => [97, new StdString('FooBar'), 5],
            'with-start'         => [97, new StdString('FooBar'), 5, 1],
            'invalid-start'      => [97, new StdString('FooBar'), 5, -1, InvalidArgumentException::class],
            'index-below-start'  => [97, new StdString('FooBar'), 2, 3, InvalidArgumentException::class],
            'index-equals-start' => [97, new StdString('FooBar'), 2, 3, InvalidArgumentException::class],
        ];
    }

    public function dataProviderCompareTo(): array
    {
        $charA = new Character('c');    // Latin
        $charB = new Character('в'); // Cyrillic

        return [
            // Latin test
            'latin-string'    => [$charA, 'c', 0],
            'latin-char'      => [$charA, new Character('c'), 0],
            'latin-higher'    => [$charA, 'd', -1],
            'latin-lower'     => [$charA, 'b', 1],

            // Cyrillic test
            'cyrillic-string' => [$charB, 'в', 0],
            'cyrillic-char'   => [$charB, new Character('в'), 0],
            'cyrillic-higher' => [$charB, 'г', -1],
            'cyrillic-lower'  => [$charB, 'б', 1],
        ];
    }

    public function dataProviderToLowerCase(): array
    {
        $charA = new Character('c'); // Latin
        $charB = new Character('б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'C'],
            'latin-char'      => [$charA, new Character('C')],
            'cyrillic-string' => [$charB, 'Б'],
            'cyrillic-char'   => [$charB, new Character('Б')],
        ];
    }

    public function dataProviderToUpperCase(): array
    {
        $charA = new Character('C'); // Latin
        $charB = new Character('Б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'c'],
            'latin-char'      => [$charA, new Character('c')],
            'cyrillic-string' => [$charB, 'б'],
            'cyrillic-char'   => [$charB, new Character('б')],
        ];
    }

    public function dataProviderValueOf(): array
    {
        $charA = new Character('c'); // Latin
        $charB = new Character('б'); // Cyrillic

        return [
            'latin-string'    => [$charA, 'c'],
            'latin-char'      => [$charA, new Character('c')],
            'cyrillic-string' => [$charB, 'б'],
            'cyrillic-char'   => [$charB, new Character('б')],
        ];
    }

    public function testCharCount(): void
    {
        self::assertEquals(1, Character::charCount('c'));
        self::assertEquals(1, Character::charCount(new Character('c')));
        self::assertEquals(1, Character::charCount('я'));
        self::assertEquals(2, Character::charCount('😁'));
    }

    public function testClone(): void
    {
        $char = new Character('c');

        self::assertInstanceOf(Character::class, $char->clone());
    }

    /**
     * @dataProvider dataProviderCodePointAt
     */
    public function testCodePointAt(
        int $expected,
        array|CharSequence $chars,
        int $index,
        int $limit = null,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, Character::codePointAt($chars, $index, $limit));
    }

    /**
     * @dataProvider dataProviderCodePointBefore
     */
    public function testCodePointBefore(
        int $expected,
        array|CharSequence $chars,
        int $index,
        int $start = null,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, Character::codePointBefore($chars, $index, $start));
    }

    /**
     * @dataProvider dataProviderCompareTo
     */
    public function testCompareTo(Character $char, string|Character $compare, int $expected): void
    {
        self::assertEquals($expected, $char->compareTo($compare));
    }

    public function testConstruct(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Character('cc');
    }

    public function testEquals(): void
    {
        // Latin base character
        self::assertTrue((new Character('c'))->equals('c'));
        self::assertTrue((new Character('c'))->equals(new Character('c')));
        self::assertFalse((new Character('c'))->equals('с')); // Cyrillic

        // Cyrillic base character
        self::assertTrue((new Character('с'))->equals('с'));
        self::assertTrue((new Character('с'))->equals(new Character('с')));
        self::assertFalse((new Character('с'))->equals('c')); // Latin
    }

    public function testHashCode(): void
    {
        $charA = new Character('c');
        $charB = new Character('с');

        self::assertEquals(spl_object_hash($charA), $charA->hashCode());
        self::assertEquals(spl_object_hash($charB), $charB->hashCode());
    }

    public function testIsLowerCase(): void
    {
        self::assertTrue(Character::isLowerCase(new Character('c'))); // Latin
        self::assertTrue(Character::isLowerCase(new Character('с'))); // Cyrillic
        self::assertTrue(Character::isLowerCase('c'));
        self::assertFalse(Character::isLowerCase(new Character('C'))); // Latin
        self::assertFalse(Character::isLowerCase(new Character('С'))); // Cyrillic
        self::assertFalse(Character::isLowerCase('C'));
    }

    public function testIsUpperCase(): void
    {
        self::assertTrue(Character::isUpperCase(new Character('C'))); // Latin
        self::assertTrue(Character::isUpperCase(new Character('С'))); // Cyrillic
        self::assertTrue(Character::isUpperCase('C'));
        self::assertFalse(Character::isUpperCase(new Character('c'))); // Latin
        self::assertFalse(Character::isUpperCase(new Character('с'))); // Cyrillic
        self::assertFalse(Character::isUpperCase('c'));
    }

    /**
     * @dataProvider dataProviderToLowerCase
     */
    public function testToLowerCase(Character $expected, string|Character $char): void
    {
        self::assertEquals($expected, Character::toLowerCase($char));
    }

    public function testToString(): void
    {
        self::assertEquals('c', (string) new Character('c')); // Latin
        self::assertEquals('с', (string) new Character('с')); // Cyrillic
    }

    /**
     * @dataProvider dataProviderToUpperCase
     */
    public function testToUpperCase(Character $expected, string|Character $char): void
    {
        self::assertEquals($expected, Character::toUpperCase($char));
    }

    /**
     * @dataProvider dataProviderValueOf
     */
    public function testValueOf(Character $expected, string|Character $char): void
    {
        self::assertEquals($expected, Character::valueOf($char));
    }
}

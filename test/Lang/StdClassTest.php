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

    public function dataProviderReplace() : array
    {
        return [
            'latin'    => ['FooBar', 'FooB', 'Saftb', $this->getObject('Saftbar')],
            'cyrillic' => ['Тест', 'ест', 'ормоз', $this->getObject('Тормоз')],
        ];
    }

    public function dataProviderReplaceAll() : array
    {
        return [
            'latin' => [
                'She sells sea shells by the sea shore.',
                '/sea/',
                'ocean',
                $this->getObject('She sells ocean shells by the ocean shore.'),
            ],
            'cyrillic' => [
                'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.',
                '/Спейси/',
                'Джеймс',
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Джеймс секс-скандал, сообщает EW.'),
            ],
        ];
    }

    public function dataProviderReplaceFirst() : array
    {
        return [
            'latin' => [
                'She sells sea shells by the sea shore.',
                '/sea/',
                'ocean',
                $this->getObject('She sells ocean shells by the sea shore.'),
            ],
            'cyrillic' => [
                'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.',
                '/Спейси/',
                'Джеймс',
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'),
            ],
        ];
    }

    public function dataProviderValueOf() : array
    {
        $obj = new Object;

        return [
            'boolean-true'  => [true, $this->getObject('true'), false],
            'boolean-false' => [false, $this->getObject('false'), false],
            'array'         => [[], $this->getObject(''), true],
            'float'         => [1.25, $this->getObject('1.25'), false],
            'integer'       => [125, $this->getObject('125'), false],
            'object'        => [$obj, $this->getObject(Object::class . '@' . spl_object_hash($obj)), false],
        ];
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

        self::assertEquals($this->getObject('B'), $stringA->charAt(3));
        self::assertEquals($this->getObject('с'), $stringB->charAt(2));
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

        self::assertEquals($this->getObject('FooBarTest'), $string->concat('Test'));
        self::assertEquals($this->getObject('FooBarTest'), $string->concat($this->getObject('Test')));
        self::assertEquals($this->getObject('FooBarТест'), $string->concat('Тест'));
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

    public function testCopyValueOf()
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        self::assertEquals($stringA, StdString::copyValueOf((string) $stringA));
        self::assertEquals($stringA, StdString::copyValueOf($stringA));

        self::assertEquals($stringB, StdString::copyValueOf((string) $stringB));
        self::assertEquals($stringB, StdString::copyValueOf($stringB));

        self::assertEquals('FooBar', StdString::copyValueOf(['F', 'o', 'o', 'B', 'a', 'r']));
    }

    public function testEndsWith()
    {
        $string = $this->getObject();

        self::assertTrue($string->endsWith('Bar'));
        self::assertFalse($string->endsWith('bar'));
        self::assertFalse($string->endsWith(null));
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

    public function testEqualsIgnoreCase()
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        self::assertTrue($stringA->equalsIgnoreCase('foobar'));
        self::assertTrue($stringA->equalsIgnoreCase($this->getObject('foobar')));
        self::assertFalse($stringA->equalsIgnoreCase('f00bar'));
        self::assertFalse($stringA->equalsIgnoreCase($this->getObject('f00bar')));

        self::assertTrue($stringB->equalsIgnoreCase('тест'));
        self::assertTrue($stringB->equalsIgnoreCase($this->getObject('тест')));
        self::assertFalse($stringB->equalsIgnoreCase('теcт')); // With latin 'c'
        self::assertFalse($stringB->equalsIgnoreCase($this->getObject('теcт'))); // With latin 'c'
    }

    public function testExplode()
    {
        $string = $this->getObject();
        self::assertEquals(['F', 'Bar'], $string->explode('oo'));
    }

    public function testFormat()
    {
        $arg1     = $this->getObject('5');
        $arg2     = 'garage';
        $expected = $this->getObject('There are 5 cars in that garage.');
        $pattern  = $this->getObject('There are %s cars in that %s.');

        self::assertEquals($expected, StdString::format($pattern, $arg1, $arg2));
    }

    public function testGetBytes()
    {
        $string = $this->getObject();
        self::assertEquals([70, 111, 111, 66, 97, 114], $string->getBytes());
    }

    public function testGetChars()
    {
        $string  = $this->getObject();
        $result1 = [];
        $result2 = [];

        $string->getChars(0, 5, $result1, 0);
        $string->getChars(1, 2, $result2, 6);

        self::assertEquals(
            [
                $this->getObject('F'),
                $this->getObject('o'),
                $this->getObject('o'),
                $this->getObject('B'),
                $this->getObject('a'),
                $this->getObject('r'),
            ],
            $result1
        );

        self::assertEquals(
            [
                6 => $this->getObject('o'),
                7 => $this->getObject('o'),
            ],
            $result2
        );
    }

    public function testHashCode()
    {
        $string = $this->getObject();
        self::assertEquals(spl_object_hash($string), $string->hashCode());
    }

    public function testIndexOf()
    {
        $string = $this->getObject();

        self::assertEquals(1, $string->indexOf('o'));
        self::assertEquals(1, $string->indexOf($this->getObject('o')));
        self::assertEquals(-1, $string->indexOf('z'));
        self::assertEquals(2, $string->indexOf('o', 2));
    }

    public function testIsEmpty()
    {
        self::assertFalse($this->getObject()->isEmpty());
        self::assertTrue($this->getObject('')->isEmpty());
    }

    public function testLastIndexOf()
    {
        $string = $this->getObject();

        self::assertEquals(2, $string->lastIndexOf('o'));
        self::assertEquals(2, $string->lastIndexOf($this->getObject('o')));
        self::assertEquals(-1, $string->lastIndexOf('z'));
        self::assertEquals(1, $string->lastIndexOf('o', -5));
        self::assertEquals(2, $string->lastIndexOf('o', 1));
    }

    public function testLength()
    {
        self::assertEquals(6, $this->getObject()->length());
        self::assertEquals(4, $this->getObject('Тест')->length());
    }

    public function testMatches()
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        self::assertTrue($stringA->matches('/Bar$/'));
        self::assertTrue($stringA->matches($this->getObject('/Bar$/')));
        self::assertFalse($stringA->matches('/Foo$/'));
        self::assertFalse($stringA->matches($this->getObject('/Foo$/')));

        self::assertTrue($stringB->matches('/ст$/'));
        self::assertTrue($stringB->matches($this->getObject('/ст$/')));
        self::assertFalse($stringB->matches('/Те$/'));
        self::assertFalse($stringB->matches($this->getObject('/Те$/')));
    }

    public function testRegionMatches()
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('ФооБарТест');

        self::assertTrue($stringA->regionMatches(3, 'TestBar', 4, 3));
        self::assertTrue($stringA->regionMatches(3, $this->getObject('TestBar'), 4, 3));
        self::assertTrue($stringA->regionMatches(3, 'Testbar', 4, 3, true));
        self::assertFalse($stringA->regionMatches(3, 'Testbar', 4, 3));
        self::assertFalse($stringA->regionMatches(3, $this->getObject('Testbar'), 4, 3));

        self::assertTrue($stringB->regionMatches(6, 'ФооТест', 3, 4));
        self::assertTrue($stringB->regionMatches(6, $this->getObject('ФооТест'), 3, 4));
        self::assertTrue($stringB->regionMatches(6, 'фоотест', 3, 4, true));
        self::assertFalse($stringB->regionMatches(6, 'фоотест', 3, 4));
        self::assertFalse($stringB->regionMatches(6, $this->getObject('фоотест'), 3, 4));
    }

    /**
     * @param   string  $string
     * @param   string  $old
     * @param   string  $new
     * @param   string  $expected
     * @dataProvider    dataProviderReplace
     */
    public function testReplace(string $string, string $old, string $new, string $expected)
    {
        $base = $this->getObject($string);

        self::assertEquals($expected, $base->replace($old, $new));
        self::assertEquals($expected, $base->replace($this->getObject($old), $this->getObject($new)));
    }

    /**
     * @param   string  $string
     * @param   string  $pattern
     * @param   string  $replacement
     * @param   string  $expected
     * @dataProvider    dataProviderReplaceAll
     */
    public function testReplaceAll(string $string, string $pattern, string $replacement, string $expected)
    {
        $base = $this->getObject($string);

        self::assertEquals($expected, $base->replaceAll($pattern, $replacement));
        self::assertEquals($expected, $base->replaceAll($this->getObject($pattern), $this->getObject($replacement)));
    }

    /**
     * @param   string  $string
     * @param   string  $pattern
     * @param   string  $replacement
     * @param   string  $expected
     * @dataProvider    dataProviderReplaceFirst
     */
    public function testReplaceFirst(string $string, string $pattern, string $replacement, string $expected)
    {
        $base = $this->getObject($string);

        self::assertEquals($expected, $base->replaceFirst($pattern, $replacement));
        self::assertEquals($expected, $base->replaceFirst($this->getObject($pattern), $this->getObject($replacement)));
    }

    public function testSplit()
    {
        self::assertEquals(
            [
                $this->getObject('F'),
                $this->getObject('Bar'),
            ],
            $this->getObject()->split('/oo/')
        );
    }

    public function testStartsWith()
    {
        self::assertTrue($this->getObject()->startsWith('Foo'));
        self::assertTrue($this->getObject()->startsWith($this->getObject('Foo')));
        self::assertFalse($this->getObject()->startsWith('Bar'));
        self::assertFalse($this->getObject()->startsWith($this->getObject('Bar')));

        self::assertTrue($this->getObject('Тест')->startsWith('Те'));
        self::assertTrue($this->getObject('Тест')->startsWith($this->getObject('Те')));
        self::assertFalse($this->getObject('Тест')->startsWith('ст'));
        self::assertFalse($this->getObject('Тест')->startsWith($this->getObject('ст')));
    }

    public function testSubSequence()
    {
        self::assertEquals(
            [
                $this->getObject('o'),
                $this->getObject('o'),
                $this->getObject('B'),
                $this->getObject('a'),
            ],
            $this->getObject()->subSequence(1, 4)
        );

        self::assertEquals(
            [
                $this->getObject('о'),
                $this->getObject('о'),
                $this->getObject('Б'),
                $this->getObject('а'),
            ],
            $this->getObject('ФооБар')->subSequence(1, 4)
        );
    }

    public function testSubstring()
    {
        self::assertEquals($this->getObject('oBa'), $this->getObject()->substring(2, 4));
        self::assertEquals($this->getObject('ест'), $this->getObject('Тест')->substring(1, 3));
    }

    public function testToCharArray()
    {
        self::assertEquals(['F', 'o', 'o', 'B', 'a', 'r'], $this->getObject()->toCharArray());
        self::assertEquals(['Ф', 'о', 'о', 'Б', 'а', 'р'], $this->getObject('ФооБар')->toCharArray());
    }

    public function testToLowercase()
    {
        self::assertEquals($this->getObject('foobar'), $this->getObject()->toLowerCase());
        self::assertEquals($this->getObject('тест'), $this->getObject('Тест')->toLowerCase());
    }

    public function testToUppercase()
    {
        self::assertEquals($this->getObject('FOOBAR'), $this->getObject()->toUpperCase());
        self::assertEquals($this->getObject('ТЕСТ'), $this->getObject('Тест')->toUpperCase());
    }

    public function testTrim()
    {
        self::assertEquals($this->getObject(), $this->getObject(' FooBar ')->trim());
        self::assertEquals($this->getObject(), $this->getObject("FooBar\n")->trim());
        self::assertEquals($this->getObject('Тест'), $this->getObject("Тест\n")->trim());
    }

    /**
     * @param   mixed   $value
     * @param   string  $expected
     * @param   boolean $throwsException
     * @dataProvider    dataProviderValueOf
     */
    public function testValueOf($value, string $expected, bool $throwsException)
    {
        if ($throwsException) {
            $this->expectException(\InvalidArgumentException::class);
        }

        self::assertEquals($expected, StdString::valueOf($value));
    }
}

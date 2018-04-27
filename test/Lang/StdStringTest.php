<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Lang;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Exception\RuntimeException;
use BlackBonjour\Stdlib\Lang\Character;
use BlackBonjour\Stdlib\Lang\StdObject;
use BlackBonjour\Stdlib\Lang\StdString;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;
use Throwable;
use TypeError;

/**
 * Test for StdString class
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       29.11.2017
 * @package     BlackBonjourTest\Stdlib\Lang
 * @copyright   Copyright (c) 2017 Erick Dyck
 * @covers      \BlackBonjour\Stdlib\Lang\StdString
 */
class StdStringTest extends TestCase
{
    public function dataProvider__construct() : array
    {
        $charArray = function (string $string) : array {
            $chars  = [];
            $length = \strlen($string);

            for ($i = 0; $i < $length; $i++) {
                $chars[] = new Character($string[$i]);
            }

            return $chars;
        };

        return [
            'valid-string'  => ['FooBar', 'FooBar'],
            'valid-class'   => [$this->getObject(), 'FooBar'],
            'valid-array'   => [$charArray('FooBar'), 'FooBar'],
            'invalid-input' => [true, '', InvalidArgumentException::class],
            'invalid-class' => [new class { public function __toString() { return 'FooBar'; }}, '', InvalidArgumentException::class],
            'invalid-array' => [['F', 'o', 'o'], '', InvalidArgumentException::class],
        ];
    }

    public function dataProviderCharAt() : array
    {
        $string = $this->getObject();

        return [
            'latin'               => [$string, 3, new Character('B')],
            'cyrillic'            => [$this->getObject('Тест'), 2, new Character('с')],
            'negative-index'      => [$string, -1, null, OutOfBoundsException::class],
            'index-equals-length' => [$string, 6, null, OutOfBoundsException::class],
            'index-above-length'  => [$string, 7, null, OutOfBoundsException::class],
        ];
    }

    public function dataProviderCompareTo() : array
    {
        $string = $this->getObject();

        return [
            'string-is-greater'     => [$string, 'foobar', -1],
            'object-is-greater'     => [$string, $this->getObject('foobar'), -1],
            'string-equals'         => [$string, 'FooBar', 0],
            'object-equals'         => [$string, $this->getObject(), 0],
            'string-is-lower'       => [$string, 'Alpha', 1],
            'object-is-lower'       => [$string, $this->getObject('Alpha'), 1],
            'invalid-compare-value' => [$string, true, 0, TypeError::class],
        ];
    }

    public function dataProviderCompareToIgnoreCase() : array
    {
        $string = $this->getObject();

        return [
            'string-is-greater'     => [$string, 'long johnson', -1],
            'object-is-greater'     => [$string, $this->getObject('long johnson'), -1],
            'string-equals'         => [$string, 'foobar', 0],
            'object-equals'         => [$string, $this->getObject('foobar'), 0],
            'string-is-lower'       => [$string, 'alpha', 1],
            'object-is-lower'       => [$string, $this->getObject('alpha'), 1],
            'invalid-compare-value' => [$string, true, 0, TypeError::class],
        ];
    }

    public function dataProviderConcat() : array
    {
        $string = $this->getObject();

        return [
            'concatenation-with-string-latin'    => [$string, 'Test', $this->getObject('FooBarTest')],
            'concatenation-with-string-cyrillic' => [$string, 'Тест', $this->getObject('FooBarТест')],
            'concatenation-with-object'          => [$string, $this->getObject('Test'), $this->getObject('FooBarTest')],
            'invalid-argument-type'              => [$string, 123, null, TypeError::class],
        ];
    }

    public function dataProviderContains() : array
    {
        $string = $this->getObject();

        return [
            'latin-string-class'    => [$string, $this->getObject('oo'), true],
            'latin-string'          => [$string, 'oo', true],
            'cyrillic-string-class' => [$string, $this->getObject('оо'), false],
            'cyrillic-string'       => [$string, 'оо', false],
            'invalid-pattern'       => [$string, 666, false],
        ];
    }

    public function dataProviderContentEquals() : array
    {
        $string = $this->getObject();

        return [
            'latin-string-class'     => [$string, $string, true],
            'latin-string'           => [$string, 'FooBar', true],
            'latin-string-lowercase' => [$string, 'foobar', false],
            'cyrillic-string-class'  => [$string, $this->getObject('Тест'), false],
            'cyrillic-string'        => [$string, 'Тест', false],
            'invalid-pattern'        => [$string, 666, false],
        ];
    }

    public function dataProviderCopyValueOf() : array
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        return [
            'latin-string-class'    => [$stringA, $stringA],
            'latin-string'          => ['FooBar', $stringA],
            'cyrillic-string-class' => [$stringB, $stringB],
            'cyrillic-string'       => ['Тест', $stringB],
            'string-list'           => [['F', 'o', 'o', 'B', 'a', 'r'], $stringA],
            'char-list'             => [[new Character('F'), new Character('o'), new Character('o'), new Character('B'), new Character('a'), new Character('r')], $stringA],
            'invalid-char-list'     => [666, $stringA, TypeError::class],
        ];
    }

    public function dataProviderEndsWith() : array
    {
        $string = $this->getObject();

        return [
            'default'          => [$string, 'Bar', false, true],
            'case-insensitive' => [$string, 'bar', true, true],
            'case-sensitive'   => [$string, 'bar', false, false],
            'invalid-pattern'  => [$string, null, false, false],
            'pattern-too-long' => [$string, 'FooBarBar', false, false],
        ];
    }

    public function dataProviderEqualsIgnoreCase() : array
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('Тест');

        return [
            'latin-string-class'             => [$stringA, $this->getObject('foobar'), true],
            'latin-string'                   => [$stringA, 'foobar', true],
            'latin-string-class-no-match'    => [$stringA, $this->getObject('f00bar'), false],
            'latin-string-no-match'          => [$stringA, 'f00bar', false],
            'cyrillic-string-class'          => [$stringB, $this->getObject('тест'), true],
            'cyrillic-string'                => [$stringB, 'тест', true],
            'cyrillic-string-class-no-match' => [$stringB, $this->getObject('теcт'), false], // With latin 'c'
            'cyrillic-string-no-match'       => [$stringB, 'теcт', false], // With latin 'c'
            'invalid-pattern'                => [$stringB, 666, false],
        ];
    }

    public function dataProviderExplode() : array
    {
        $string = $this->getObject();

        return [
            'default'         => [$string, 'oo', ['F', 'Bar']],
            'object-pattern'  => [$string, $this->getObject('oo'), ['F', 'Bar']],
            'invalid-pattern' => [$string, 666, ['F', 'Bar'], TypeError::class],
            'empty-pattern'   => [$string, '', ['F', 'Bar'], RuntimeException::class], // Should trigger a warning
        ];
    }

    public function dataProviderFormat() : array
    {
        return [
            'string-pattern' => [
                'There are %d cars in that %s.',
                [5, $this->getObject('garage')],
                $this->getObject('There are 5 cars in that garage.'),
            ],
            'object-pattern' => [
                $this->getObject('%d chinese men with a %s'),
                [3, $this->getObject('contrabass')],
                $this->getObject('3 chinese men with a contrabass'),
            ],
            'invalid-pattern' => [123, [], null, TypeError::class],
        ];
    }

    public function dataProviderIndexOf() : array
    {
        $string = $this->getObject();

        return [
            'string-needle'       => [$string, 'o', 1],
            'object-needle'       => [$string, $this->getObject('o'), 1],
            'with-offset'         => [$string, 'o', 2, 2],
            'negative-offset'     => [$string, 'F', -1, -2],
            'needle-non-existent' => [$string, 'z', -1],
            'invalid-needle'      => [$string, 123, -1, 0, TypeError::class],
        ];
    }

    public function dataProviderLastIndexOf() : array
    {
        $string = $this->getObject();

        return [
            'string-needle'       => [$string, 'o', 2],
            'object-needle'       => [$string, $this->getObject('o'), 2],
            'with-offset'         => [$string, 'o', 2, 1],
            'negative-offset'     => [$string, 'o', 1, -5],
            'needle-non-existent' => [$string, 'z', -1],
            'invalid-needle'      => [$string, 123, -1, 0, TypeError::class],
        ];
    }

    public function dataProviderMatches() : array
    {
        $string = $this->getObject();

        return [
            'string-pattern'         => [$string, '/Bar$/', true],
            'object-pattern'         => [$string, $this->getObject('/Bar$/'), true],
            'cyrillic-pattern'       => [$this->getObject('Тест'), '/ст$/', true],
            'pattern-does-not-match' => [$string, '/Foo$/', false],
            'invalid-pattern'        => [$string, 123, false, TypeError::class],
        ];
    }

    public function dataProviderOffsetExists() : array
    {
        $string = $this->getObject();

        return [
            'offset-does-exist'     => [$string, 3, true],
            'offset-does-not-exist' => [$string, 6, false],
            'offset-numeric-string' => [$string, '3', true],
            'offset-illegal'        => [$string, 'o', false],
            'offset-negative'       => [$string, -2, false],
        ];
    }

    public function dataProviderOffsetGet() : array
    {
        $string = $this->getObject();

        return [
            'offset-does-exist'     => [$string, 3, 'B'],
            'offset-does-not-exist' => [$string, 6, '', OutOfBoundsException::class],
            'offset-numeric-string' => [$string, '3', 'B'],
            'offset-illegal'        => [$string, 'o', 'F'], // Should trigger a warning and return first letter
            'offset-negative'       => [$string, -2, '', OutOfBoundsException::class],
        ];
    }

    public function dataProviderOffsetSet() : array
    {
        return [
            'offset-does-exist'     => [$this->getObject(), 3, 'C', 'FooCar'],
            'offset-does-not-exist' => [$this->getObject(), 7, 'Donald Duck', 'FooBar Donald Duck'],
            'offset-numeric-string' => [$this->getObject(), '3', 'C', 'FooCar'],
            'offset-illegal'        => [$this->getObject(), 'o', '', 'FooBar'], // Should trigger a warning
            'offset-negative'       => [$this->getObject(), -2, '', 'FooBar'], // Should trigger a warning
            'offset-set-last-index' => [$this->getObject(), 5, 'zz', 'FooBazz'],
        ];
    }

    public function dataProviderRegionMatches() : array
    {
        $stringA = $this->getObject();
        $stringB = $this->getObject('ФооБарТест');

        return [
            'latin-string-class'          => [$stringA, 3, $this->getObject('TestBar'), 4, 3, false, true],
            'latin-string'                => [$stringA, 3, 'TestBar', 4, 3, false, true],
            'latin-case-insensitive'      => [$stringA, 3, 'Testbar', 4, 3, true, true],
            'latin-case-sensitive'        => [$stringA, 3, 'Testbar', 4, 3, false, false],
            'latin-case-sensitive-obj'    => [$stringA, 3, $this->getObject('Testbar'), 4, 3, false, false],
            'cyrillic-string-class'       => [$stringB, 6, $this->getObject('ФооТест'), 3, 4, false, true],
            'cyrillic-string'             => [$stringB, 6, 'ФооТест', 3, 4, false, true],
            'cyrillic-case-insensitive'   => [$stringB, 6, 'фоотест', 3, 4, true, true],
            'cyrillic-case-sensitive'     => [$stringB, 6, 'фоотест', 3, 4, false, false],
            'cyrillic-case-sensitive-obj' => [$stringB, 6, $this->getObject('фоотест'), 3, 4, false, false],
            'negative-offset'             => [$stringA, -1, 'TestBar', 4, 3, false, false],
            'negative-pattern-offset'     => [$stringA, 3, 'TestBar', -1, 3, false, false],
            'pattern-offset-too-high'     => [$stringA, 3, 'TestBar', 5, 3, false, false],
            'string-offset-too-high'      => [$stringA, 4, 'TestBar', 4, 3, false, false],
            'invalid-string'              => [$stringA, 3, 123, 4, 3, false, false, TypeError::class],
        ];
    }

    public function dataProviderReplace() : array
    {
        $string = $this->getObject();

        return [
            'string-replacement'  => [$string, 'FooB', 'Saftb', $this->getObject('Saftbar')],
            'object-replacement'  => [$string, $this->getObject('FooB'), $this->getObject('Saftb'), $this->getObject('Saftbar')],
            'cyrillic'            => [$this->getObject('Тест'), 'ест', 'ормоз', $this->getObject('Тормоз')],
            'invalid-needle'      => [$string, 123, 'Saftb', null, TypeError::class],
            'invalid-replacement' => [$string, 'FooB', 123, null, TypeError::class],
        ];
    }

    public function dataProviderReplaceAll() : array
    {
        return [
            'string-pattern-and-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                '/sea/',
                'ocean',
                $this->getObject('She sells ocean shells by the ocean shore.'),
            ],
            'object-pattern-and-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                $this->getObject('/sea/'),
                $this->getObject('ocean'),
                $this->getObject('She sells ocean shells by the ocean shore.'),
            ],
            'cyrillic-pattern-and-replacement' => [
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'),
                '/Спейси/',
                'Джеймс',
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Джеймс секс-скандал, сообщает EW.'),
            ],
            'invalid-pattern' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                123,
                'ocean',
                $this->getObject('She sells ocean shells by the ocean shore.'),
                TypeError::class,
            ],
            'invalid-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                '/sea/',
                123,
                $this->getObject('She sells ocean shells by the ocean shore.'),
                TypeError::class,
            ],
        ];
    }

    public function dataProviderReplaceFirst() : array
    {
        return [
            'string-pattern-and-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                '/sea/',
                'ocean',
                $this->getObject('She sells ocean shells by the sea shore.'),
            ],
            'object-pattern-and-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                $this->getObject('/sea/'),
                $this->getObject('ocean'),
                $this->getObject('She sells ocean shells by the sea shore.'),
            ],
            'cyrillic-pattern-and-replacement' => [
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'),
                '/Спейси/',
                'Джеймс',
                $this->getObject('Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'),
            ],
            'invalid-pattern' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                123,
                'ocean',
                $this->getObject('She sells ocean shells by the sea shore.'),
                TypeError::class,
            ],
            'invalid-replacement' => [
                $this->getObject('She sells sea shells by the sea shore.'),
                '/sea/',
                123,
                $this->getObject('She sells ocean shells by the sea shore.'),
                TypeError::class,
            ],
        ];
    }

    public function dataProviderSplit() : array
    {
        $string = $this->getObject();

        return [
            'string-pattern'       => [$string, '/oo/', [$this->getObject('F'), $this->getObject('Bar')]],
            'object-pattern'       => [$string, $this->getObject('/oo/'), [$this->getObject('F'), $this->getObject('Bar')]],
            'invalid-pattern'      => [$string, 'oo', [$this->getObject('F'), $this->getObject('Bar')], -1, RuntimeException::class],
            'invalid-pattern-type' => [$string, 123, [$this->getObject('F'), $this->getObject('Bar')], -1, TypeError::class],
        ];
    }

    public function dataProviderStartsWith() : array
    {
        $string = $this->getObject();

        return [
            'string-needle'        => [$string, 'Foo', true],
            'object-needle'        => [$string, $this->getObject('Foo'), true],
            'cyrillic'             => [$this->getObject('Тест'), 'Те', true],
            'needled-non-existent' => [$string, 'baz', false],
            'invalid-needle'       => [$string, 123, false],
        ];
    }

    public function dataProviderValueOf() : array
    {
        $obj = new StdObject;

        return [
            'boolean-true'   => [true, $this->getObject('true')],
            'boolean-false'  => [false, $this->getObject('false')],
            'array'          => [[], null, true],
            'float'          => [1.25, $this->getObject('1.25')],
            'integer'        => [125, $this->getObject('125')],
            'std-object'     => [$obj, $this->getObject(StdObject::class . '@' . spl_object_hash($obj))],
            'invalid-object' => [new \stdClass(), null, true],
            'object'         => [new class {public function __toString() { return 'FooBar'; }}, $this->getObject()],
        ];
    }

    private function getObject(string $string = 'FooBar') : StdString
    {
        return new StdString($string);
    }

    /**
     * @param StdString|Character[]|string $string
     * @param string                       $expectation
     * @param string                       $exception
     * @dataProvider dataProvider__construct
     */
    public function test__construct($string, string $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $stdString = new StdString($string);
        self::assertEquals($expectation, (string) $stdString);
    }

    public function test__toString() : void
    {
        $string = $this->getObject();
        self::assertEquals('FooBar', (string) $string);
    }

    /**
     * @param StdString $string
     * @param int       $index
     * @param Character $expectation
     * @param string    $exception
     * @dataProvider dataProviderCharAt
     */
    public function testCharAt(StdString $string, int $index, ?Character $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->charAt($index));
    }

    public function testCodePointAt() : void
    {
        self::assertEquals(97, $this->getObject()->codePointAt(4));
        self::assertEquals(1041, $this->getObject('ФооБар')->codePointAt(3));
    }

    public function testCodePointBefore() : void
    {
        self::assertEquals(97, $this->getObject()->codePointBefore(5));
        self::assertEquals(1041, $this->getObject('ФооБар')->codePointBefore(4));
    }

    public function testClone() : void
    {
        $string = $this->getObject();
        self::assertInstanceOf(StdString::class, $string->clone());
    }

    /**
     * @param StdString        $string
     * @param StdString|string $compareValue
     * @param int              $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderCompareTo
     */
    public function testCompareTo(StdString $string, $compareValue, int $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->compareTo($compareValue));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $compareValue
     * @param int              $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderCompareToIgnoreCase
     */
    public function testCompareToIgnoreCase(
        StdString $string,
        $compareValue,
        int $expectation,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->compareToIgnoreCase($compareValue));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $value
     * @param StdString        $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderConcat
     */
    public function testConcat(StdString $string, $value, $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->concat($value));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param boolean          $expectation
     * @dataProvider dataProviderContains
     */
    public function testContains(StdString $string, $pattern, bool $expectation) : void
    {
        self::assertEquals($expectation, $string->contains($pattern));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param boolean          $expectation
     * @dataProvider dataProviderContentEquals
     */
    public function testContentEquals(StdString $string, $pattern, bool $expectation) : void
    {
        self::assertEquals($expectation, $string->contentEquals($pattern));
    }

    /**
     * @param StdString|string|array $charList
     * @param StdString              $expectation
     * @param string                 $exception
     * @throws TypeError
     * @dataProvider dataProviderCopyValueOf
     */
    public function testCopyValueOf($charList, StdString $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, StdString::copyValueOf($charList));
    }

    public function testCount() : void
    {
        self::assertCount(6, $this->getObject()); // Latin
        self::assertCount(6, $this->getObject('ФооБар')); // Cyrillic
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param boolean          $caseInsensitive
     * @param boolean          $expectation
     * @dataProvider dataProviderEndsWith
     */
    public function testEndsWith(StdString $string, $pattern, bool $caseInsensitive, bool $expectation) : void
    {
        self::assertEquals($expectation, $string->endsWith($pattern, $caseInsensitive));
    }

    public function testEquals() : void
    {
        $objA = $this->getObject();
        $objB = clone $objA;
        $objC = $this->getObject('Dis I Like');
        $objD = new StdObject;

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
        self::assertFalse($objA->equals($objD));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param boolean          $expectation
     * @dataProvider dataProviderEqualsIgnoreCase
     */
    public function testEqualsIgnoreCase(StdString $string, $pattern, bool $expectation) : void
    {
        self::assertEquals($expectation, $string->equalsIgnoreCase($pattern));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param StdString[]      $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderExplode
     */
    public function testExplode(StdString $string, $pattern, array $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->explode($pattern));
    }

    /**
     * @param StdString|string $pattern
     * @param array            $arguments
     * @param StdString        $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderFormat
     */
    public function testFormat($pattern, array $arguments, $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, StdString::format($pattern, ...$arguments));
    }

    public function testGetBytes() : void
    {
        $string = $this->getObject();
        self::assertEquals([70, 111, 111, 66, 97, 114], $string->getBytes());
    }

    public function testGetChars() : void
    {
        $string  = $this->getObject();
        $result1 = [];
        $result2 = [];

        $string->getChars(0, 5, $result1, 0);
        $string->getChars(1, 2, $result2, 6);

        self::assertEquals(
            [
                new Character('F'),
                new Character('o'),
                new Character('o'),
                new Character('B'),
                new Character('a'),
                new Character('r'),
            ],
            $result1
        );

        self::assertEquals(
            [
                6 => new Character('o'),
                7 => new Character('o'),
            ],
            $result2
        );
    }

    public function testHashCode() : void
    {
        $string = $this->getObject();
        self::assertEquals(spl_object_hash($string), $string->hashCode());
    }

    /**
     * @param StdString        $string
     * @param StdString|string $needle
     * @param int              $expectation
     * @param int              $offset
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderIndexOf
     */
    public function testIndexOf(
        StdString $string,
        $needle,
        int $expectation,
        int $offset = 0,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->indexOf($needle, $offset));
    }

    public function testIsEmpty() : void
    {
        self::assertFalse($this->getObject()->isEmpty());
        self::assertTrue($this->getObject('')->isEmpty());
    }

    /**
     * @param StdString        $string
     * @param StdString|string $needle
     * @param StdString        $expectation
     * @param int              $offset
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderLastIndexOf
     */
    public function testLastIndexOf(
        StdString $string,
        $needle,
        $expectation,
        int $offset = 0,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->lastIndexOf($needle, $offset));
    }

    public function testLength() : void
    {
        self::assertEquals(6, $this->getObject()->length());
        self::assertEquals(4, $this->getObject('Тест')->length());
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param boolean          $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderMatches
     */
    public function testMatches(StdString $string, $pattern, bool $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->matches($pattern));
    }

    /**
     * @param StdString $string
     * @param mixed     $offset
     * @param boolean   $expectation
     * @dataProvider dataProviderOffsetExists
     */
    public function testOffsetExists(StdString $string, $offset, bool $expectation) : void
    {
        self::assertEquals($expectation, isset($string[$offset]));
    }

    /**
     * @param StdString $string
     * @param mixed     $offset
     * @param string    $expectation
     * @param string    $exception
     * @dataProvider dataProviderOffsetGet
     */
    public function testOffsetGet(StdString $string, $offset, string $expectation, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string[$offset]);
    }

    /**
     * @param StdString $string
     * @param mixed     $offset
     * @param mixed     $value
     * @param string    $expectation
     * @dataProvider dataProviderOffsetSet
     */
    public function testOffsetSet(StdString $string, $offset, $value, string $expectation) : void
    {
        $string[$offset] = $value;
        self::assertEquals($expectation, (string) $string);
    }

    public function testOffsetUnset() : void
    {
        $string = $this->getObject();

        try {
            unset($string[1]);
        } catch (Throwable $t) {
            self::assertInstanceOf(Error::class, $t);
            return;
        }

        $this->fail('Shouldn\'t be reachable!');
    }

    /**
     * @param StdString        $stringA
     * @param int              $offset
     * @param StdString|string $pattern
     * @param int              $strOffset
     * @param int              $length
     * @param boolean          $ignoreCase
     * @param boolean          $expectation
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderRegionMatches
     */
    public function testRegionMatches(
        StdString $stringA,
        int $offset,
        $pattern,
        int $strOffset,
        int $length,
        bool $ignoreCase,
        bool $expectation,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $stringA->regionMatches($offset, $pattern, $strOffset, $length, $ignoreCase));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $old
     * @param StdString|string $new
     * @param StdString        $expected
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderReplace
     */
    public function testReplace(StdString $string, $old, $new, $expected, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replace($old, $new));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param StdString|string $replacement
     * @param StdString        $expected
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderReplaceAll
     */
    public function testReplaceAll(
        StdString $string,
        $pattern,
        $replacement,
        $expected,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replaceAll($pattern, $replacement));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param StdString|string $replacement
     * @param StdString        $expected
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderReplaceFirst
     */
    public function testReplaceFirst(
        StdString $string,
        $pattern,
        $replacement,
        $expected,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replaceFirst($pattern, $replacement));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $pattern
     * @param StdString|string $expectation
     * @param int              $limit
     * @param string           $exception
     * @throws TypeError
     * @dataProvider dataProviderSplit
     */
    public function testSplit(
        StdString $string,
        $pattern,
        $expectation,
        int $limit = -1,
        string $exception = null
    ) : void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expectation, $string->split($pattern, $limit));
    }

    /**
     * @param StdString        $string
     * @param StdString|string $needle
     * @param boolean          $expectation
     * @param int              $offset
     * @dataProvider dataProviderStartsWith
     */
    public function testStartsWith(StdString $string, $needle, bool $expectation, int $offset = 0) : void
    {
        self::assertEquals($expectation, $string->startsWith($needle, $offset));
    }

    public function testSubSequence() : void
    {
        self::assertEquals(
            [
                new Character('o'),
                new Character('o'),
                new Character('B'),
                new Character('a'),
            ],
            $this->getObject()->subSequence(1, 4)
        );

        self::assertEquals(
            [
                new Character('о'),
                new Character('о'),
                new Character('Б'),
                new Character('а'),
            ],
            $this->getObject('ФооБар')->subSequence(1, 4)
        );

        $this->expectException(OutOfBoundsException::class);
        self::assertEquals([new Character('o'), new Character('o')], $this->getObject()->subSequence(-1, 2));
    }

    public function testSubstr() : void
    {
        self::assertEquals($this->getObject('oBa'), $this->getObject()->substr(2, 3));
        self::assertEquals($this->getObject('oBar'), $this->getObject()->substr(2));
        self::assertEquals($this->getObject('ест'), $this->getObject('Тест')->substr(1, 3));

        $this->expectException(InvalidArgumentException::class);
        $this->getObject()->substr(-1, 3);
    }

    public function testSubstring() : void
    {
        self::assertEquals($this->getObject('oBa'), $this->getObject()->substring(2, 4));
        self::assertEquals($this->getObject('oBar'), $this->getObject()->substring(2));
        self::assertEquals($this->getObject('ест'), $this->getObject('Тест')->substring(1, 3));
    }

    public function testToCharArray() : void
    {
        // Latin
        $result = $this->getObject()->toCharArray();

        self::assertInstanceOf(Character::class, reset($result));
        self::assertEquals([
            new Character('F'),
            new Character('o'),
            new Character('o'),
            new Character('B'),
            new Character('a'),
            new Character('r'),
        ], $result);

        // Cyrillic
        $result = $this->getObject('ФооБар')->toCharArray();

        self::assertInstanceOf(Character::class, reset($result));
        self::assertEquals([
            new Character('Ф'),
            new Character('о'),
            new Character('о'),
            new Character('Б'),
            new Character('а'),
            new Character('р'),
        ], $result);
    }

    public function testToLowercase() : void
    {
        self::assertEquals($this->getObject('foobar'), $this->getObject()->toLowerCase());
        self::assertEquals($this->getObject('тест'), $this->getObject('Тест')->toLowerCase());
    }

    public function testToUppercase() : void
    {
        self::assertEquals($this->getObject('FOOBAR'), $this->getObject()->toUpperCase());
        self::assertEquals($this->getObject('ТЕСТ'), $this->getObject('Тест')->toUpperCase());
    }

    public function testTrim() : void
    {
        self::assertEquals($this->getObject(), $this->getObject(' FooBar ')->trim());
        self::assertEquals($this->getObject(), $this->getObject("FooBar\n")->trim());
        self::assertEquals($this->getObject('Тест'), $this->getObject("Тест\n")->trim());
    }

    /**
     * @param mixed     $value
     * @param StdString $expected
     * @param boolean   $throwsException
     * @dataProvider dataProviderValueOf
     */
    public function testValueOf($value, $expected, bool $throwsException = false) : void
    {
        if ($throwsException) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertEquals($expected, StdString::valueOf($value));
    }
}

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
use stdClass;
use Throwable;
use TypeError;

use function strlen;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     29.11.2017
 * @copyright Copyright (c) 2017 Erick Dyck
 */
class StdStringTest extends TestCase
{
    public function dataProviderCharAt(): array
    {
        $string = new StdString('FooBar');

        return [
            'latin'               => [$string, 3, new Character('B')],
            'cyrillic'            => [new StdString('Тест'), 2, new Character('с')],
            'negative-index'      => [$string, -1, new Character('r')],
            'index-equals-length' => [$string, 6, null, OutOfBoundsException::class],
            'index-above-length'  => [$string, 7, null, OutOfBoundsException::class],
        ];
    }

    public function dataProviderCompareTo(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-is-greater'     => [$string, 'foobar', -1],
            'object-is-greater'     => [$string, new StdString('foobar'), -1],
            'string-equals'         => [$string, 'FooBar', 0],
            'object-equals'         => [$string, new StdString('FooBar'), 0],
            'string-is-lower'       => [$string, 'Alpha', 1],
            'object-is-lower'       => [$string, new StdString('Alpha'), 1],
            'invalid-compare-value' => [$string, true, 0, TypeError::class],
        ];
    }

    public function dataProviderCompareToIgnoreCase(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-is-greater'     => [$string, 'long johnson', -1],
            'object-is-greater'     => [$string, new StdString('long johnson'), -1],
            'string-equals'         => [$string, 'foobar', 0],
            'object-equals'         => [$string, new StdString('foobar'), 0],
            'string-is-lower'       => [$string, 'alpha', 1],
            'object-is-lower'       => [$string, new StdString('alpha'), 1],
            'invalid-compare-value' => [$string, true, 0, TypeError::class],
        ];
    }

    public function dataProviderConcat(): array
    {
        $string = new StdString('FooBar');

        return [
            'concatenation-with-string-latin'    => [$string, 'Test', new StdString('FooBarTest')],
            'concatenation-with-string-cyrillic' => [$string, 'Тест', new StdString('FooBarТест')],
            'concatenation-with-object'          => [$string, new StdString('Test'), new StdString('FooBarTest')],
            'invalid-argument-type'              => [$string, 123, null, TypeError::class],
        ];
    }

    public function dataProviderConstruct(): array
    {
        $charArray = static function (string $string): array {
            $chars  = [];
            $length = strlen($string);

            for ($i = 0; $i < $length; $i++) {
                $chars[] = new Character($string[$i]);
            }

            return $chars;
        };

        $anonymousClass = new class {
            public function __toString(): string
            {
                return 'FooBar';
            }
        };

        return [
            'valid-string'  => ['FooBar', 'FooBar'],
            'valid-class'   => [new StdString('FooBar'), 'FooBar'],
            'valid-array'   => [$charArray('FooBar'), 'FooBar'],
            'invalid-input' => [true, '', InvalidArgumentException::class],
            'invalid-class' => [$anonymousClass, '', InvalidArgumentException::class],
            'invalid-array' => [['F', 'o', 'o'], '', InvalidArgumentException::class],
        ];
    }

    public function dataProviderContains(): array
    {
        $string = new StdString('FooBar');

        return [
            'latin-string-class'    => [$string, new StdString('oo'), true],
            'latin-string'          => [$string, 'oo', true],
            'cyrillic-string-class' => [$string, new StdString('оо'), false],
            'cyrillic-string'       => [$string, 'оо', false],
        ];
    }

    public function dataProviderContentEquals(): array
    {
        $string = new StdString('FooBar');

        return [
            'latin-string-class'     => [$string, $string, true],
            'latin-string'           => [$string, 'FooBar', true],
            'latin-string-lowercase' => [$string, 'foobar', false],
            'cyrillic-string-class'  => [$string, new StdString('Тест'), false],
            'cyrillic-string'        => [$string, 'Тест', false],
        ];
    }

    public function dataProviderCopyValueOf(): array
    {
        $stringA = new StdString('FooBar');
        $stringB = new StdString('Тест');

        return [
            'latin-string-class'    => [$stringA, $stringA],
            'latin-string'          => ['FooBar', $stringA],
            'cyrillic-string-class' => [$stringB, $stringB],
            'cyrillic-string'       => ['Тест', $stringB],
            'string-list'           => [['F', 'o', 'o', 'B', 'a', 'r'], $stringA],
            'char-list'             => [
                [
                    new Character('F'),
                    new Character('o'),
                    new Character('o'),
                    new Character('B'),
                    new Character('a'),
                    new Character('r'),
                ],
                $stringA,
            ],
            'invalid-char-list'     => [666, $stringA, TypeError::class],
        ];
    }

    public function dataProviderEndsWith(): array
    {
        $string = new StdString('FooBar');

        return [
            'default'          => [$string, 'Bar', false, true],
            'case-insensitive' => [$string, 'bar', true, true],
            'case-sensitive'   => [$string, 'bar', false, false],
            'pattern-too-long' => [$string, 'FooBarBar', false, false],
        ];
    }

    public function dataProviderEqualsIgnoreCase(): array
    {
        $stringA = new StdString('FooBar');
        $stringB = new StdString('Тест');

        return [
            'latin-string-class'             => [$stringA, new StdString('foobar'), true],
            'latin-string'                   => [$stringA, 'foobar', true],
            'latin-string-class-no-match'    => [$stringA, new StdString('f00bar'), false],
            'latin-string-no-match'          => [$stringA, 'f00bar', false],
            'cyrillic-string-class'          => [$stringB, new StdString('тест'), true],
            'cyrillic-string'                => [$stringB, 'тест', true],
            'cyrillic-string-class-no-match' => [$stringB, new StdString('теcт'), false], // With latin 'c'
            'cyrillic-string-no-match'       => [$stringB, 'теcт', false], // With latin 'c'
        ];
    }

    public function dataProviderExplode(): array
    {
        $string = new StdString('FooBar');

        return [
            'default'         => [$string, 'oo', ['F', 'Bar']],
            'object-pattern'  => [$string, new StdString('oo'), ['F', 'Bar']],
            'invalid-pattern' => [$string, 666, ['F', 'Bar'], TypeError::class],
            'empty-pattern'   => [$string, '', ['F', 'Bar'], InvalidArgumentException::class],
        ];
    }

    public function dataProviderFormat(): array
    {
        return [
            'string-pattern'  => [
                'There are %d cars in that %s.',
                [5, new StdString('garage')],
                new StdString('There are 5 cars in that garage.'),
            ],
            'object-pattern'  => [
                new StdString('%d chinese men with a %s'),
                [3, new StdString('contrabass')],
                new StdString('3 chinese men with a contrabass'),
            ],
            'invalid-pattern' => [123, [], null, TypeError::class],
        ];
    }

    public function dataProviderIndexOf(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-needle'       => [$string, 'o', 1],
            'object-needle'       => [$string, new StdString('o'), 1],
            'with-offset'         => [$string, 'o', 2, 2],
            'negative-offset'     => [$string, 'F', -1, -2],
            'needle-non-existent' => [$string, 'z', -1],
            'invalid-needle'      => [$string, 123, -1, 0, TypeError::class],
        ];
    }

    public function dataProviderLastIndexOf(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-needle'       => [$string, 'o', 2],
            'object-needle'       => [$string, new StdString('o'), 2],
            'with-offset'         => [$string, 'o', 2, 1],
            'negative-offset'     => [$string, 'o', 1, -5],
            'needle-non-existent' => [$string, 'z', -1],
            'invalid-needle'      => [$string, 123, -1, 0, TypeError::class],
        ];
    }

    public function dataProviderMatches(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-pattern'         => [$string, '/Bar$/', true],
            'object-pattern'         => [$string, new StdString('/Bar$/'), true],
            'cyrillic-pattern'       => [new StdString('Тест'), '/ст$/', true],
            'pattern-does-not-match' => [$string, '/Foo$/', false],
            'invalid-pattern'        => [$string, 123, false, TypeError::class],
        ];
    }

    public function dataProviderOffsetExists(): array
    {
        $string = new StdString('FooBar');

        return [
            'offset-does-exist'             => [$string, 3, true],
            'offset-does-not-exist'         => [$string, 6, false],
            'offset-numeric-string'         => [$string, '3', true],
            'offset-illegal'                => [$string, 'o', false],
            'offset-negative'               => [$string, -2, true],
            'offset-negative-out-of-bounce' => [$string, -7, false],
        ];
    }

    public function dataProviderOffsetGet(): array
    {
        $string = new StdString('FooBar');

        return [
            'offset-does-exist'             => [$string, 3, 'B'],
            'offset-does-not-exist'         => [$string, 6, '', OutOfBoundsException::class],
            'offset-numeric-string'         => [$string, '3', 'B'],
            'offset-illegal'                => [$string, 'o', 'F'], // Should trigger a warning and return first letter
            'offset-negative'               => [$string, -2, 'a'],
            'offset-negative-out-of-bounce' => [$string, -7, '', OutOfBoundsException::class],
        ];
    }

    public function dataProviderOffsetSet(): array
    {
        return [
            'offset-does-exist'     => [new StdString('FooBar'), 3, 'C', 'FooCar'],
            'offset-does-not-exist' => [new StdString('FooBar'), 7, 'Donald Duck', 'FooBar Donald Duck'],
            'offset-numeric-string' => [new StdString('FooBar'), '3', 'C', 'FooCar'],
            'offset-illegal'        => [new StdString('FooBar'), 'o', '', 'FooBar'], // Should trigger a warning
            'offset-negative'       => [new StdString('FooBar'), -2, '', 'FooBar'], // Should trigger a warning
            'offset-negative-fail'  => [new StdString('FooBar'), -7, '', 'FooBar'], // Should trigger a warning
            'offset-set-last-index' => [new StdString('FooBar'), 5, 'zz', 'FooBazz'],
        ];
    }

    public function dataProviderRegionMatches(): array
    {
        $stringA = new StdString('FooBar');
        $stringB = new StdString('ФооБарТест');

        return [
            'latin-string-class'          => [$stringA, 3, new StdString('TestBar'), 4, 3, false, true],
            'latin-string'                => [$stringA, 3, 'TestBar', 4, 3, false, true],
            'latin-case-insensitive'      => [$stringA, 3, 'Testbar', 4, 3, true, true],
            'latin-case-sensitive'        => [$stringA, 3, 'Testbar', 4, 3, false, false],
            'latin-case-sensitive-obj'    => [$stringA, 3, new StdString('Testbar'), 4, 3, false, false],
            'cyrillic-string-class'       => [$stringB, 6, new StdString('ФооТест'), 3, 4, false, true],
            'cyrillic-string'             => [$stringB, 6, 'ФооТест', 3, 4, false, true],
            'cyrillic-case-insensitive'   => [$stringB, 6, 'фоотест', 3, 4, true, true],
            'cyrillic-case-sensitive'     => [$stringB, 6, 'фоотест', 3, 4, false, false],
            'cyrillic-case-sensitive-obj' => [$stringB, 6, new StdString('фоотест'), 3, 4, false, false],
            'negative-offset'             => [$stringA, -1, 'TestBar', 4, 3, false, false],
            'negative-pattern-offset'     => [$stringA, 3, 'TestBar', -1, 3, false, false],
            'pattern-offset-too-high'     => [$stringA, 3, 'TestBar', 5, 3, false, false],
            'string-offset-too-high'      => [$stringA, 4, 'TestBar', 4, 3, false, false],
            'invalid-string'              => [$stringA, 3, 123, 4, 3, false, false, TypeError::class],
        ];
    }

    public function dataProviderReplace(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-replacement'  => [$string, 'FooB', 'Saftb', new StdString('Saftbar')],
            'object-replacement'  => [
                $string,
                new StdString('FooB'),
                new StdString('Saftb'),
                new StdString('Saftbar'),
            ],
            'cyrillic'            => [new StdString('Тест'), 'ест', 'ормоз', new StdString('Тормоз')],
            'invalid-needle'      => [$string, 123, 'Saftb', null, TypeError::class],
            'invalid-replacement' => [$string, 'FooB', 123, null, TypeError::class],
        ];
    }

    public function dataProviderReplaceAll(): array
    {
        return [
            'string-pattern-and-replacement'   => [
                new StdString('She sells sea shells by the sea shore.'),
                '/sea/',
                'ocean',
                new StdString('She sells ocean shells by the ocean shore.'),
            ],
            'object-pattern-and-replacement'   => [
                new StdString('She sells sea shells by the sea shore.'),
                new StdString('/sea/'),
                new StdString('ocean'),
                new StdString('She sells ocean shells by the ocean shore.'),
            ],
            'cyrillic-pattern-and-replacement' => [
                new StdString(
                    'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'
                ),
                '/Спейси/',
                'Джеймс',
                new StdString(
                    'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Джеймс секс-скандал, сообщает EW.'
                ),
            ],
            'invalid-pattern'                  => [
                new StdString('She sells sea shells by the sea shore.'),
                123,
                'ocean',
                new StdString('She sells ocean shells by the ocean shore.'),
                TypeError::class,
            ],
            'invalid-replacement'              => [
                new StdString('She sells sea shells by the sea shore.'),
                '/sea/',
                123,
                new StdString('She sells ocean shells by the ocean shore.'),
                TypeError::class,
            ],
        ];
    }

    public function dataProviderReplaceFirst(): array
    {
        return [
            'string-pattern-and-replacement'   => [
                new StdString('She sells sea shells by the sea shore.'),
                '/sea/',
                'ocean',
                new StdString('She sells ocean shells by the sea shore.'),
            ],
            'object-pattern-and-replacement'   => [
                new StdString('She sells sea shells by the sea shore.'),
                new StdString('/sea/'),
                new StdString('ocean'),
                new StdString('She sells ocean shells by the sea shore.'),
            ],
            'cyrillic-pattern-and-replacement' => [
                new StdString(
                    'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Спейси из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'
                ),
                '/Спейси/',
                'Джеймс',
                new StdString(
                    'Режиссеру Риддли Скотту пришлось вырезать все сцены с участием Кевина Джеймс из нового трейлера фильма "Все деньги мира", который выйдет на экраны в конце декабря. Причина столь радикальной редактуры – вспыхнувший вокруг Спейси секс-скандал, сообщает EW.'
                ),
            ],
            'invalid-pattern'                  => [
                new StdString('She sells sea shells by the sea shore.'),
                123,
                'ocean',
                new StdString('She sells ocean shells by the sea shore.'),
                TypeError::class,
            ],
            'invalid-replacement'              => [
                new StdString('She sells sea shells by the sea shore.'),
                '/sea/',
                123,
                new StdString('She sells ocean shells by the sea shore.'),
                TypeError::class,
            ],
        ];
    }

    public function dataProviderSplit(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-pattern'       => [$string, '/oo/', [new StdString('F'), new StdString('Bar')]],
            'object-pattern'       => [
                $string,
                new StdString('/oo/'),
                [new StdString('F'), new StdString('Bar')],
            ],
            'invalid-pattern'      => [
                $string,
                'oo',
                [new StdString('F'), new StdString('Bar')],
                -1,
                RuntimeException::class,
            ],
            'invalid-pattern-type' => [
                $string,
                123,
                [new StdString('F'), new StdString('Bar')],
                -1,
                TypeError::class,
            ],
        ];
    }

    public function dataProviderStartsWith(): array
    {
        $string = new StdString('FooBar');

        return [
            'string-needle'        => [$string, 'Foo', true],
            'object-needle'        => [$string, new StdString('Foo'), true],
            'cyrillic'             => [new StdString('Тест'), 'Те', true],
            'needled-non-existent' => [$string, 'baz', false],
        ];
    }

    public function dataProviderValueOf(): array
    {
        $obj            = new StdObject();
        $anonymousClass = new class {
            public function __toString(): string
            {
                return 'FooBar';
            }
        };

        return [
            'boolean-true'   => [true, new StdString('true')],
            'boolean-false'  => [false, new StdString('false')],
            'array'          => [[], null, true],
            'float'          => [1.25, new StdString('1.25')],
            'integer'        => [125, new StdString('125')],
            'std-object'     => [$obj, new StdString(StdObject::class . '@' . spl_object_hash($obj))],
            'invalid-object' => [new stdClass(), null, true],
            'object'         => [$anonymousClass, new StdString('FooBar')],
        ];
    }

    /**
     * @dataProvider dataProviderCharAt
     */
    public function testCharAt(StdString $string, int $index, ?Character $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->charAt($index));
    }

    public function testClone(): void
    {
        $string = new StdString('FooBar');

        self::assertInstanceOf(StdString::class, $string->clone());
    }

    public function testCodePointAt(): void
    {
        self::assertEquals(97, (new StdString('FooBar'))->codePointAt(4));
        self::assertEquals(1041, (new StdString('ФооБар'))->codePointAt(3));
    }

    public function testCodePointBefore(): void
    {
        self::assertEquals(97, (new StdString('FooBar'))->codePointBefore(5));
        self::assertEquals(1041, (new StdString('ФооБар'))->codePointBefore(4));
    }

    /**
     * @dataProvider dataProviderCompareTo
     */
    public function testCompareTo(StdString $string, $compareValue, int $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->compareTo($compareValue));
    }

    /**
     * @dataProvider dataProviderCompareToIgnoreCase
     */
    public function testCompareToIgnoreCase(
        StdString $string,
        $compareValue,
        int $expected,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->compareToIgnoreCase($compareValue));
    }

    /**
     * @dataProvider dataProviderConcat
     */
    public function testConcat(StdString $string, $value, ?StdString $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->concat($value));
    }

    public function testConstruct(): void
    {
        self::assertEquals('FooBar', (string) (new StdString('FooBar')));
    }

    /**
     * @dataProvider dataProviderContains
     */
    public function testContains(StdString $string, string|StdString $pattern, bool $expected): void
    {
        self::assertEquals($expected, $string->contains($pattern));
    }

    /**
     * @dataProvider dataProviderContentEquals
     */
    public function testContentEquals(StdString $string, string|StdString $pattern, bool $expected): void
    {
        self::assertEquals($expected, $string->contentEquals($pattern));
    }

    /**
     * @dataProvider dataProviderCopyValueOf
     */
    public function testCopyValueOf($charList, StdString $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, StdString::copyValueOf($charList));
    }

    public function testCount(): void
    {
        self::assertCount(6, new StdString('FooBar')); // Latin
        self::assertCount(6, new StdString('ФооБар')); // Cyrillic
    }

    public function testCreateFromArrayOfChar(): void
    {
        $chars = [
            new Character('F'),
            new Character('o'),
            new Character('o'),
            new Character('B'),
            new Character('a'),
            new Character('r'),
        ];

        self::assertEquals('FooBar', (string) StdString::createFromArrayOfChar($chars));
    }

    public function testCreateFromArrayOfCharException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Array of %s is required!', Character::class));

        $chars = [
            new Character('F'),
            new Character('o'),
            new Character('o'),
            new Character('B'),
            'a',
            new Character('r'),
        ];

        StdString::createFromArrayOfChar($chars);
    }

    /**
     * @dataProvider dataProviderEndsWith
     */
    public function testEndsWith(
        StdString $string,
        string|StdString $pattern,
        bool $caseInsensitive,
        bool $expected
    ): void {
        self::assertEquals($expected, $string->endsWith($pattern, $caseInsensitive));
    }

    public function testEquals(): void
    {
        $objA = new StdString('FooBar');
        $objB = clone $objA;
        $objC = new StdString('Dis I Like');
        $objD = new StdObject();

        self::assertTrue($objA->equals($objB));
        self::assertFalse($objA->equals($objC));
        self::assertFalse($objA->equals($objD));
    }

    /**
     * @dataProvider dataProviderEqualsIgnoreCase
     */
    public function testEqualsIgnoreCase(StdString $string, string|StdString $pattern, bool $expected): void
    {
        self::assertEquals($expected, $string->equalsIgnoreCase($pattern));
    }

    /**
     * @param StdString[] $expected
     *
     * @dataProvider dataProviderExplode
     */
    public function testExplode(StdString $string, $pattern, array $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->explode($pattern));
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat($pattern, array $arguments, ?StdString $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, StdString::format($pattern, ...$arguments));
    }

    public function testGetBytes(): void
    {
        $string = new StdString('FooBar');

        self::assertEquals([70, 111, 111, 66, 97, 114], $string->getBytes());
    }

    public function testGetChars(): void
    {
        $string  = new StdString('FooBar');
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

    public function testHashCode(): void
    {
        $string = new StdString('FooBar');

        self::assertEquals(spl_object_hash($string), $string->hashCode());
    }

    /**
     * @dataProvider dataProviderIndexOf
     */
    public function testIndexOf(
        StdString $string,
        $needle,
        int $expected,
        int $offset = 0,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->indexOf($needle, $offset));
    }

    public function testIsEmpty(): void
    {
        self::assertFalse((new StdString('FooBar'))->isEmpty());
        self::assertTrue((new StdString(''))->isEmpty());
    }

    /**
     * @dataProvider dataProviderLastIndexOf
     */
    public function testLastIndexOf(
        StdString $string,
        $needle,
        int $expected,
        int $offset = 0,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->lastIndexOf($needle, $offset));
    }

    public function testLength(): void
    {
        self::assertEquals(6, (new StdString('FooBar'))->length());
        self::assertEquals(4, (new StdString('Тест'))->length());
    }

    /**
     * @dataProvider dataProviderMatches
     */
    public function testMatches(StdString $string, $pattern, bool $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->matches($pattern));
    }

    /**
     * @dataProvider dataProviderOffsetExists
     */
    public function testOffsetExists(StdString $string, $offset, bool $expected): void
    {
        self::assertEquals($expected, isset($string[$offset]));
    }

    /**
     * @dataProvider dataProviderOffsetGet
     */
    public function testOffsetGet(StdString $string, $offset, string $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string[$offset]);
    }

    /**
     * @dataProvider dataProviderOffsetSet
     */
    public function testOffsetSet(StdString $string, $offset, $value, string $expected): void
    {
        $string[$offset] = $value;

        self::assertEquals($expected, (string) $string);
    }

    public function testOffsetUnset(): void
    {
        $string = new StdString('FooBar');

        try {
            unset($string[1]);
        } catch (Throwable $t) {
            self::assertInstanceOf(Error::class, $t);

            return;
        }

        self::fail('Shouldn\'t be reachable!');
    }

    /**
     * @dataProvider dataProviderRegionMatches
     */
    public function testRegionMatches(
        StdString $stringA,
        int $offset,
        $pattern,
        int $strOffset,
        int $length,
        bool $ignoreCase,
        bool $expected,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $stringA->regionMatches($offset, $pattern, $strOffset, $length, $ignoreCase));
    }

    /**
     * @dataProvider dataProviderReplace
     */
    public function testReplace(StdString $string, $old, $new, ?StdString $expected, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replace($old, $new));
    }

    /**
     * @dataProvider dataProviderReplaceAll
     */
    public function testReplaceAll(
        StdString $string,
        $pattern,
        $replacement,
        ?StdString $expected,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replaceAll($pattern, $replacement));
    }

    /**
     * @dataProvider dataProviderReplaceFirst
     */
    public function testReplaceFirst(
        StdString $string,
        $pattern,
        $replacement,
        ?StdString $expected,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->replaceFirst($pattern, $replacement));
    }

    /**
     * @dataProvider dataProviderSplit
     */
    public function testSplit(
        StdString $string,
        $pattern,
        $expected,
        int $limit = -1,
        string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertEquals($expected, $string->split($pattern, $limit));
    }

    /**
     * @dataProvider dataProviderStartsWith
     */
    public function testStartsWith(StdString $string, string|StdString $needle, bool $expected, int $offset = 0): void
    {
        self::assertEquals($expected, $string->startsWith($needle, $offset));
    }

    public function testSubSequence(): void
    {
        self::assertEquals(
            [
                new Character('o'),
                new Character('o'),
                new Character('B'),
                new Character('a'),
            ],
            (new StdString('FooBar'))->subSequence(1, 4)
        );

        self::assertEquals(
            [
                new Character('о'),
                new Character('о'),
                new Character('Б'),
                new Character('а'),
            ],
            (new StdString('ФооБар'))->subSequence(1, 4)
        );

        $this->expectException(OutOfBoundsException::class);

        self::assertEquals([new Character('o'), new Character('o')], (new StdString('FooBar'))->subSequence(-1, 2));
    }

    public function testSubstr(): void
    {
        self::assertEquals(new StdString('oBa'), (new StdString('FooBar'))->substr(2, 3));
        self::assertEquals(new StdString('oBar'), (new StdString('FooBar'))->substr(2));
        self::assertEquals(new StdString('ест'), (new StdString('Тест'))->substr(1, 3));

        $this->expectException(InvalidArgumentException::class);
        (new StdString('FooBar'))->substr(-1, 3);
    }

    public function testSubstring(): void
    {
        self::assertEquals(new StdString('oBa'), (new StdString('FooBar'))->substring(2, 4));
        self::assertEquals(new StdString('oBar'), (new StdString('FooBar'))->substring(2));
        self::assertEquals(new StdString('ест'), (new StdString('Тест'))->substring(1, 3));
    }

    public function testToCharArray(): void
    {
        // Latin
        $result = (new StdString('FooBar'))->toCharArray();

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
        $result = (new StdString('ФооБар'))->toCharArray();

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

    public function testToLowercase(): void
    {
        self::assertEquals(new StdString('foobar'), (new StdString('FooBar'))->toLowerCase());
        self::assertEquals(new StdString('тест'), (new StdString('Тест'))->toLowerCase());
    }

    public function testToString(): void
    {
        $string = new StdString('FooBar');

        self::assertEquals('FooBar', (string) $string);
    }

    public function testToUppercase(): void
    {
        self::assertEquals(new StdString('FOOBAR'), (new StdString('FooBar'))->toUpperCase());
        self::assertEquals(new StdString('ТЕСТ'), (new StdString('Тест'))->toUpperCase());
    }

    public function testTrim(): void
    {
        self::assertEquals(new StdString('FooBar'), (new StdString(' FooBar '))->trim());
        self::assertEquals(new StdString('FooBar'), (new StdString("FooBar\n"))->trim());
        self::assertEquals(new StdString('Тест'), (new StdString("Тест\n"))->trim());
    }

    /**
     * @dataProvider dataProviderValueOf
     */
    public function testValueOf($value, ?StdString $expected, bool $throwsException = false): void
    {
        if ($throwsException) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertEquals($expected, StdString::valueOf($value));
    }
}

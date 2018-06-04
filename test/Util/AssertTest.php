<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Lang\Character as Char;
use BlackBonjour\Stdlib\Lang\StdString;
use BlackBonjour\Stdlib\Util\Assert;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * Unit test for Assert class
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     06.02.2018
 * @package   BlackBonjourTest\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 * @covers    \BlackBonjour\Stdlib\Util\Assert
 */
class AssertTest extends TestCase
{
    public function dataProviderTypeOf() : array
    {
        return [
            // Valid
            'one-type-as-string-one-value'   => ['string', ['FooBar']],
            'one-type-as-array-one-value'    => [['string'], ['FooBar']],
            'multiple-types-one-value'       => [['integer', 'double'], [12]],
            'multiple-types-multiple-values' => [['integer', 'double'], [12, 12.3]],
            'char-array'                     => [[Char::class], [new Char, new Char, new Char]],
            'string-array'                   => [[Char::class, StdString::class], [new Char, new StdString, new Char]],
            'inheritance'                    => [[Char::class], [new class extends Char { /* Just a test */ }]],

            // Invalid
            'invalid-assertion'  => [null, ['FooBar'], InvalidArgumentException::class],
            'invalid-type'       => ['integer', ['223'], TypeError::class],
            'invalid-instance'   => [StdString::class, [new Char], TypeError::class],
            'invalid-char-array' => [Char::class, [new Char, new StdString, new Char], TypeError::class],
        ];
    }

    public function testEmpty() : void
    {
        self::assertTrue(Assert::empty('', 0, false, []));
        self::assertFalse(Assert::empty('', 1, false, []));
    }

    /**
     * @param mixed  $types
     * @param array  $values
     * @param string $exception
     * @throws InvalidArgumentException
     * @throws TypeError
     * @dataProvider dataProviderTypeOf
     */
    public function testTypeOf($types, array $values, string $exception = null) : void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertTrue(Assert::typeOf($types, ...$values));
    }

    /**
     * @param mixed  $types
     * @param array  $values
     * @param string $exception
     * @dataProvider dataProviderTypeOf
     */
    public function testValidate($types, array $values, string $exception = null) : void
    {
        self::assertEquals($exception === null, Assert::validate($types, ...$values));
    }
}

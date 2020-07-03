<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Lang\Character as Char;
use BlackBonjour\Stdlib\Lang\StdString;
use BlackBonjour\Stdlib\Util\Assert;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     06.02.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class AssertTest extends TestCase
{
    public function dataProviderTypeOf(): array
    {
        $anonymousClass = new class extends Char {
            /* Just a test */
        };

        return [
            // Valid
            'one-type-as-string-one-value'   => ['string', ['FooBar']],
            'one-type-as-array-one-value'    => [['string'], ['FooBar']],
            'multiple-types-one-value'       => [['integer', 'double'], [12]],
            'multiple-types-multiple-values' => [['integer', 'double'], [12, 12.3]],
            'char-array'                     => [[Char::class], [new Char(), new Char(), new Char()]],
            'string-array'                   => [
                [Char::class, StdString::class],
                [new Char(), new StdString(), new Char()],
            ],
            'inheritance'                    => [[Char::class], [$anonymousClass]],
            'issue-33-simple-object-check'   => ['object', [new stdClass()]],

            // Invalid
            'invalid-assertion'              => [null, ['FooBar'], InvalidArgumentException::class],
            'invalid-type'                   => ['integer', ['223'], TypeError::class],
            'invalid-instance'               => [StdString::class, [new Char()], TypeError::class],
            'invalid-char-array'             => [
                Char::class,
                [new Char(), new StdString(), new Char()],
                TypeError::class,
            ],
        ];
    }

    public function testEmpty(): void
    {
        self::assertTrue(Assert::empty('', 0, false, []));
        self::assertFalse(Assert::empty('', 1, false, []));
    }

    public function testHandleInvalidArguments(): void
    {
        $this->expectException(TypeError::class);

        Assert::empty();
    }

    public function testNotEmpty(): void
    {
        self::assertTrue(Assert::notEmpty(123, '123', true));
        self::assertFalse(Assert::notEmpty(123, 0, true));
    }

    /**
     * @param mixed $types
     * @dataProvider dataProviderTypeOf
     */
    public function testTypeOf($types, array $values, string $exception = null): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertTrue(Assert::typeOf($types, ...$values));
    }

    /**
     * @param mixed $types
     * @dataProvider dataProviderTypeOf
     */
    public function testValidate($types, array $values, string $exception = null): void
    {
        self::assertEquals($exception === null, Assert::validate($types, ...$values));
    }
}

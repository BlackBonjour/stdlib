<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Lang\Character as Char;
use BlackBonjour\Stdlib\Lang\StdString;
use BlackBonjour\Stdlib\Util\Assert;
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
        $anonymousClass = new class ('F') extends Char {
            /* Just a test */
        };

        return [
            // Valid
            'one-type-as-string-one-value'   => [
                Assert::TYPE_STRING,
                ['FooBar'],
            ],
            'one-type-as-array-one-value'    => [
                [Assert::TYPE_STRING],
                ['FooBar'],
            ],
            'multiple-types-one-value'       => [
                [Assert::TYPE_INTEGER, Assert::TYPE_DOUBLE],
                [12],
            ],
            'multiple-types-multiple-values' => [
                [Assert::TYPE_INTEGER, Assert::TYPE_DOUBLE],
                [12, 12.3],
            ],
            'char-array'                     => [
                [Char::class],
                [new Char('F'), new Char('F'), new Char('F')],
            ],
            'string-array'                   => [
                [Char::class, StdString::class],
                [new Char('F'), new StdString(), new Char('F')],
            ],
            'inheritance'                    => [
                [Char::class],
                [$anonymousClass],
            ],
            'issue-33-simple-object-check'   => [
                Assert::TYPE_OBJECT,
                [new stdClass()],
            ],
            'issue-36-null-check'            => [
                Assert::TYPE_NULL,
                [null],
            ],

            // Invalid
            'invalid-type'                   => [
                'integer',
                ['223'],
                TypeError::class,
            ],
            'invalid-instance'               => [
                StdString::class,
                [new Char('F')],
                TypeError::class,
            ],
            'invalid-char-array'             => [
                Char::class,
                [new Char('F'), new StdString(), new Char('F')],
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
     * @dataProvider dataProviderTypeOf
     */
    public function testValidate($types, array $values, string $exception = null): void
    {
        self::assertEquals($exception === null, Assert::validate($types, ...$values));
    }
}

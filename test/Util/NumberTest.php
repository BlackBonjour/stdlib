<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\Number;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * Unit test for number util
 *
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     28.04.2018
 * @package   BlackBonjourTest\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 * @covers    \BlackBonjour\Stdlib\Util\Number
 */
class NumberTest extends TestCase
{
    public function testOrdinal() : void
    {
        self::assertEquals(Number::ORDINAL_ST, Number::ordinal(1));
        self::assertEquals(Number::ORDINAL_ST, Number::ordinal(21));
        self::assertEquals(Number::ORDINAL_ND, Number::ordinal(2));
        self::assertEquals(Number::ORDINAL_ND, Number::ordinal(22));
        self::assertEquals(Number::ORDINAL_RD, Number::ordinal(3));
        self::assertEquals(Number::ORDINAL_RD, Number::ordinal(23));
        self::assertEquals(Number::ORDINAL_TH, Number::ordinal(4));
        self::assertEquals(Number::ORDINAL_TH, Number::ordinal(11));
        self::assertEquals(Number::ORDINAL_TH, Number::ordinal(12));
        self::assertEquals(Number::ORDINAL_TH, Number::ordinal(13));
        self::assertEquals(Number::ORDINAL_TH, Number::ordinal(24));

        $this->expectException(TypeError::class);
        Number::ordinal('foobar');
    }

    public function testOrdinalize() : void
    {
        self::assertEquals(1 . Number::ORDINAL_ST, Number::ordinalize(1));
        self::assertEquals(21 . Number::ORDINAL_ST, Number::ordinalize(21));
        self::assertEquals(2 . Number::ORDINAL_ND, Number::ordinalize(2));
        self::assertEquals(22 . Number::ORDINAL_ND, Number::ordinalize(22));
        self::assertEquals(3 . Number::ORDINAL_RD, Number::ordinalize(3));
        self::assertEquals(23 . Number::ORDINAL_RD, Number::ordinalize(23));
        self::assertEquals(4 . Number::ORDINAL_TH, Number::ordinalize(4));
        self::assertEquals(11 . Number::ORDINAL_TH, Number::ordinalize(11));
        self::assertEquals(12 . Number::ORDINAL_TH, Number::ordinalize(12));
        self::assertEquals(13 . Number::ORDINAL_TH, Number::ordinalize(13));
        self::assertEquals(24 . Number::ORDINAL_TH, Number::ordinalize(24));
    }
}

<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     25.03.2019
 * @copyright Copyright (c) 2019 Erick Dyck
 */
class ArrayUtilsTest extends TestCase
{
    public function testFlatten(): void
    {
        self::assertEquals(['foo', 'bar'], ArrayUtils::flatten([['foo'], ['bar']]));
        self::assertEmpty(ArrayUtils::flatten([]));
        self::assertEmpty(ArrayUtils::flatten([[], []]));
    }
}

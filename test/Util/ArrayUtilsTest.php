<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     25.03.2019
 * @package   BlackBonjourTest\Stdlib\Util
 * @copyright Copyright (c) 2019 Erick Dyck
 * @covers    \BlackBonjour\Stdlib\Util\ArrayUtils
 */
class ArrayUtilsTest extends TestCase
{
    public function testFlatten(): void
    {
        self::assertEquals(['foo', 'bar'], ArrayUtils::flatten([['foo'], ['bar']]));
    }
}

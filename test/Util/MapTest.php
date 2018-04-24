<?php
declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Util\Map;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for map
 *
 * @author      Erick Dyck <info@erickdyck.de>
 * @since       24.04.2018
 * @package     BlackBonjourTest\Stdlib\Util
 * @copyright   Copyright (c) 2018 Erick Dyck
 */
class MapTest extends TestCase
{
    public function testArrayAccess() : void
    {
        $map        = new Map;
        $map['foo'] = 'bar';
        $map[12345] = 67890;

        self::assertTrue(isset($map['12345'], $map['foo']));

        unset($map['12345']);

        self::assertFalse(isset($map['12345']));
        self::assertEquals('bar', $map['foo']);
    }
}

<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Util\Map;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     24.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class MapTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $map        = new Map();
        $map['foo'] = 'bar';
        $map[12345] = 67890;

        self::assertTrue(isset($map['12345'], $map['foo']));

        unset($map['12345']);

        self::assertFalse(isset($map['12345']));
        self::assertEquals('bar', $map['foo']);
        self::assertCount(1, $map);
    }

    public function testClearAndSize(): void
    {
        $map        = new Map();
        $map['foo'] = uniqid('foo_', false);

        self::assertEquals(1, $map->size());

        $map->clear();
        self::assertEquals(0, $map->size());
    }

    public function testContainsKey(): void
    {
        $map        = new Map();
        $map['foo'] = 'bar';
        $map[123]   = 456;

        self::assertTrue($map->containsKey('foo'));
        self::assertTrue($map->containsKey(123));
        self::assertFalse($map->containsKey('bar'));
    }

    public function testContainsKeyThrowsOutOfBoundsException(): void
    {
        $this->expectException(TypeError::class);

        (new Map())->containsKey([]);
    }

    public function testContainsValue(): void
    {
        $map        = new Map();
        $map['foo'] = 'bar';

        self::assertTrue($map->containsValue('bar'));
        self::assertFalse($map->containsValue('baz'));
    }

    public function testCreateFromArray(): void
    {
        $map = Map::createFromArray(
            [
                'foo' => 'bar',
                'baz' => 'lorem',
            ]
        );

        self::assertCount(2, $map);
        self::assertTrue(isset($map['foo']));
        self::assertTrue(isset($map['baz']));
        self::assertFalse(isset($map['bar']));
    }

    public function testGet(): void
    {
        $map        = new Map();
        $map['foo'] = 'bar';

        self::assertEquals('bar', $map->get('foo'));
    }

    public function testGetThrowsOutOfBoundsException(): void
    {
        $this->expectException(OutOfBoundsException::class);

        (new Map())->get('baz');
    }

    public function testGetThrowsTypeError(): void
    {
        $this->expectException(TypeError::class);

        (new Map())->get([]);
    }

    public function testIsEmpty(): void
    {
        $map = new Map();
        self::assertTrue($map->isEmpty());

        $map['foo'] = 'bar';
        self::assertFalse($map->isEmpty());

        $map->clear();
        self::assertTrue($map->isEmpty());
    }

    public function testIterable(): void
    {
        $map = Map::createFromArray(
            [
                'foo' => 'bar',
                'baz' => 'lorem',
            ]
        );

        $firstValue = $map->current();

        foreach ($map as $key => $value) {
            usleep(100);
        }

        self::assertEquals('baz', $key);
        self::assertEquals('lorem', $value);
        self::assertNull($map->key());
        self::assertFalse($map->current());

        $map->rewind();
        self::assertEquals($firstValue, $map->current());
    }

    public function testPut(): void
    {
        $map = new Map();
        self::assertTrue($map->isEmpty());

        $map->put('foo', 'bar');
        self::assertTrue($map->containsKey('foo'));
        self::assertEquals('bar', $map->get('foo'));
    }

    public function testPutThrowsTypeError(): void
    {
        $this->expectException(TypeError::class);

        (new Map())->put([], 'bar');
    }

    public function testPutAll(): void
    {
        $mapFoo = new Map();
        $mapFoo->put('foo', 'bar');
        self::assertCount(1, $mapFoo);

        $mapFoo->putAll((new Map())->put('baz', 'lorem'));
        self::assertCount(2, $mapFoo);
        self::assertEquals('lorem', $mapFoo->get('baz'));
    }

    public function testRemove(): void
    {
        $map        = new Map();
        $map['foo'] = 'bar';

        self::assertCount(1, $map);

        $map->remove('foo');
        self::assertCount(0, $map);

        $this->expectException(TypeError::class);
        $map->remove([]);
    }

    public function testToArray(): void
    {
        $map = (new Map())
            ->put(123, 'foo')
            ->put('bar', 'baz');

        self::assertEquals(['123' => 'foo', 'bar' => 'baz'], $map->toArray());
    }

    public function testValues(): void
    {
        self::assertEquals(['bar', 'lorem'], (new Map())->put('foo', 'bar')->put('baz', 'lorem')->values());
    }
}

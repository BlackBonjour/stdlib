<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use ArrayObject;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Lang\StdString;
use BlackBonjour\Stdlib\Util\HashMap;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     28.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class HashMapTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $map        = new HashMap();
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
        $map = new HashMap();
        $map->put(new stdClass(), new ArrayObject(['foo' => 'bar']));

        self::assertEquals(1, $map->size());

        $map->clear();
        self::assertEquals(0, $map->size());
    }

    public function testContainsKey(): void
    {
        $stdClass = new stdClass();
        $map      = new HashMap();
        $map->put($stdClass, new ArrayObject(['foo' => 'bar']));

        self::assertTrue($map->containsKey($stdClass));
        self::assertFalse($map->containsKey(new stdClass()));
    }

    public function testContainsValue(): void
    {
        $arrayObject = new ArrayObject();
        $map         = new HashMap();
        $map->put(new stdClass(), $arrayObject);

        self::assertTrue($map->containsValue($arrayObject));
        self::assertFalse($map->containsValue(new ArrayObject()));
    }

    public function testCreateFromArray(): void
    {
        $map = HashMap::createFromArray(
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

    public function testCurrent(): void
    {
        $map = HashMap::createFromArray(['some-key' => null]);

        self::assertEquals('some-key', $map->key());
        self::assertNull($map->current());
    }

    public function testGet(): void
    {
        $arrayObject = new ArrayObject();
        $map         = new HashMap();
        $stdClass    = new stdClass();

        $map->put($stdClass, $arrayObject);

        self::assertEquals($arrayObject, $map->get($stdClass));
    }

    public function testGetThrowsOutOfBoundsException(): void
    {
        $this->expectException(OutOfBoundsException::class);

        self::assertNull((new HashMap())->get(new stdClass()));
    }

    public function testIsEmpty(): void
    {
        $map = new HashMap();
        self::assertTrue($map->isEmpty());

        $map['foo'] = 'bar';
        self::assertFalse($map->isEmpty());

        $map->clear();
        self::assertTrue($map->isEmpty());
    }

    public function testIterable(): void
    {
        $map = HashMap::createFromArray(
            [
                'foo' => 'bar',
                'baz' => 'lorem',
            ]
        );

        $firstKey   = $map->key();
        $firstValue = $map->current();

        foreach ($map as $key => $value) {
            usleep(100);
        }

        self::assertEquals('baz', $key);
        self::assertEquals('lorem', $value);
        self::assertNull($map->key());
        self::assertFalse($map->current());

        $map->rewind();
        self::assertEquals($firstKey, $map->key());
        self::assertEquals($firstValue, $map->current());
    }

    public function testKey(): void
    {
        $map = new HashMap();
        self::assertNull($map->key());

        $map->put(new stdClass(), ['foo' => 'bar']);
        self::assertInstanceOf(stdClass::class, $map->key());
    }

    public function testPut(): void
    {
        $map = new HashMap();
        self::assertTrue($map->isEmpty());

        $map->put('foo', 'bar');
        self::assertTrue($map->containsKey('foo'));
        self::assertEquals('bar', $map->get('foo'));

        $array    = ['foo', ['bar']];
        $stdClass = new stdClass();

        $map->put($array, $stdClass);
        self::assertTrue($map->containsKey($array));
        self::assertEquals($stdClass, $map->get($array));
    }

    public function testPutAll(): void
    {
        $mapFoo = new HashMap();
        $mapFoo->put('foo', 'bar');
        self::assertCount(1, $mapFoo);

        $mapFoo->putAll((new HashMap())->put('baz', 'lorem'));
        self::assertCount(2, $mapFoo);
        self::assertEquals('lorem', $mapFoo->get('baz'));
    }

    public function testRemove(): void
    {
        $map        = new HashMap();
        $map['foo'] = 'bar';

        self::assertCount(1, $map);

        $map->remove('foo');
        self::assertCount(0, $map);
    }

    public function testSort(): void
    {
        $exampleKey   = new StdString('FooBar');
        $exampleValue = [123, 456];
        $hashMap      = new HashMap();
        $hashMap
            ->put($exampleKey, $exampleValue)
            ->put(new StdString('BarFoo'), [456, 789])
            ->put(new StdString('FooBaz'), [123, 789]);

        // Key sort
        $keySort = clone $hashMap;
        $keySort->sort('strcmp', true);

        foreach (['BarFoo', 'FooBar', 'FooBaz'] as $expectedKey) {
            self::assertEquals($expectedKey, (string) $keySort->key());

            $keySort->next();
        }

        self::assertEquals($exampleValue, $keySort->get($exampleKey));

        // Value sort
        $valueSort = clone $hashMap;
        $valueSort->sort(static function ($a, $b): int {
            [$a1, $a2] = $a;
            [$b1, $b2] = $b;

            return ($a1 + $a2) <=> ($b1 + $b2);
        });

        foreach (['FooBar', 'FooBaz', 'BarFoo'] as $expectedKey) {
            self::assertEquals($expectedKey, (string) $valueSort->key());

            $valueSort->next();
        }

        self::assertEquals($exampleValue, $keySort->get($exampleKey));
    }

    public function testToArray(): void
    {
        $hashMap = (new HashMap())
            ->put(123, 'foo')
            ->put(456, 'bar');

        self::assertEquals(['foo', 'bar'], $hashMap->toArray());
    }

    public function testValues(): void
    {
        self::assertEquals(['bar', 'lorem'], (new HashMap())->put('foo', 'bar')->put('baz', 'lorem')->values());
    }
}

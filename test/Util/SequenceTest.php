<?php

declare(strict_types=1);

namespace BlackBonjourTest\Stdlib\Util;

use BlackBonjour\Stdlib\Exception\InvalidArgumentException;
use BlackBonjour\Stdlib\Exception\OutOfBoundsException;
use BlackBonjour\Stdlib\Util\Map;
use BlackBonjour\Stdlib\Util\Sequence;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     25.03.2019
 * @copyright Copyright (c) 2019 Erick Dyck
 */
class SequenceTest extends TestCase
{
    public function testArrayAccess(): void
    {
        $sequence = (new Sequence())->pushAll(['Foo', 'Bar', 'Baz']);

        self::assertTrue(isset($sequence[2]));
        self::assertEquals('Bar', $sequence[1]);

        unset($sequence[2]);
        self::assertCount(2, $sequence);

        $sequence[2] = 'Lorem Ipsum';
        self::assertCount(3, $sequence);
    }

    public function testClear(): void
    {
        $sequence = (new Sequence())->pushAll(['foo', 'bar']);
        self::assertCount(2, $sequence);

        $sequence->clear();
        self::assertCount(0, $sequence);
    }

    public function testContainsValue(): void
    {
        $sequence = (new Sequence())->pushAll(['foo', 'bar']);

        self::assertTrue($sequence->containsValue('foo'));
        self::assertFalse($sequence->containsValue('baz'));
    }

    public function testCreateFromArray(): void
    {
        $expectation = (new Sequence())
            ->push('foo')
            ->push('bar');

        self::assertEquals($expectation, Sequence::createFromArray(['foo', 'bar']));
    }

    public function testFill(): void
    {
        $sequence = (new Sequence())
            ->pushAll(['Foo', 'Bar', 'Baz'])
            ->fill('Lorem Ipsum');

        self::assertEquals(array_fill(0, 3, 'Lorem Ipsum'), $sequence->toArray());
    }

    public function testGet(): void
    {
        $sequence = (new Sequence())->push('foo');
        self::assertEquals('foo', $sequence->get(0));

        $this->expectException(OutOfBoundsException::class);
        $sequence->get(1);
    }

    public function testHandleInvalidKey(): void
    {
        $this->expectException(TypeError::class);

        $sequence = (new Sequence())->push('foo');
        $sequence->get([]);
    }

    public function testIsEmpty(): void
    {
        $sequence = new Sequence();
        self::assertTrue($sequence->isEmpty());

        $sequence->push('foo');
        self::assertFalse($sequence->isEmpty());
    }

    public function testKey(): void
    {
        $sequence = (new Sequence())->push('foo');
        self::assertEquals(0, $sequence->key());

        $sequence->next();
        self::assertEquals(null, $sequence->key());
    }

    public function testPush(): void
    {
        $sequence = (new Sequence())->push('FooBar');
        self::assertEquals('FooBar', $sequence->current());

        $sequence->push('Lorem Ipsum', 3);
        self::assertEquals(['FooBar', 'Lorem Ipsum', 'Lorem Ipsum', 'Lorem Ipsum'], $sequence->toArray());

        $this->expectException(InvalidArgumentException::class);
        $sequence->push('foo', -2);
    }

    public function testPushAll(): void
    {
        $sequence = (new Sequence())->pushAll(['Foo', 'Bar', 'Baz']);

        self::assertEquals(['Foo', 'Bar', 'Baz'], $sequence->toArray());
    }

    public function testPut(): void
    {
        $sequence = (new Sequence())->put(0, 'foo');
        self::assertEquals('foo', $sequence->get(0));

        $sequence->put(0, 'bar');
        self::assertEquals('bar', $sequence->get(0));
    }

    public function testPutAll(): void
    {
        $sequence = (new Sequence())->putAll(Map::createFromArray(['foo', 'bar']));

        self::assertEquals('foo', $sequence->get(0));
        self::assertEquals('bar', $sequence->get(1));
    }

    public function testRemove(): void
    {
        $sequence = (new Sequence())->pushAll(['foo', 'bar']);
        self::assertCount(2, $sequence);

        $sequence->remove(1);
        self::assertCount(1, $sequence);
        self::assertEquals('foo', $sequence->get(0));
    }

    public function testRewind(): void
    {
        $sequence = (new Sequence())->pushAll(['foo', 'bar']);

        $sequence->next();
        self::assertEquals('bar', $sequence->current());

        $sequence->rewind();
        self::assertEquals('foo', $sequence->current());
    }

    public function testValid(): void
    {
        $sequence = (new Sequence())->pushAll(['foo']);
        self::assertTrue($sequence->valid());

        $sequence->next();
        self::assertFalse($sequence->valid());
    }

    public function testValues(): void
    {
        $sequence = (new Sequence())->pushAll([1 => 'foo', 2 => 'bar']);

        self::assertEquals(['foo', 'bar'], $sequence->values());
    }
}

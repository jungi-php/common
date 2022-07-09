<?php

namespace Jungi\Common\Tests;

use Jungi\Common\Equatable;
use PHPUnit\Framework\TestCase;
use function Jungi\Common\array_equals;
use function Jungi\Common\equals;
use function Jungi\Common\in_iterable;
use function Jungi\Common\iterable_search;
use function Jungi\Common\iterable_unique;

/**
 * @author Piotr Kugla <piku235@gmail.com>
 */
class FunctionsTest extends TestCase
{
    public function testResultAliases(): void
    {
        $r = ok(123);
        $this->assertTrue($r->isOk());
        $this->assertEquals(123, $r->get());

        $r = err(123);
        $this->assertTrue($r->isErr());
        $this->assertEquals(123, $r->getErr());
    }

    public function testOptionAliases(): void
    {
        $op = some(123);
        $this->assertTrue($op->isSome());
        $this->assertEquals(123, $op->get());

        $op = none();
        $this->assertTrue($op->isNone());
    }

    /** @dataProvider provideEqualVariables */
    public function testThatTwoVariablesEqual($a, $b): void
    {
        $this->assertTrue(equals($a, $b));
    }

    /** @dataProvider provideNotEqualVariables */
    public function testThatTwoVariablesNotEqual($a, $b): void
    {
        $this->assertFalse(equals($a, $b));
    }

    /** @dataProvider providePresentValuesInIterables */
    public function testThatValueIsInIterable($value, $iterable): void
    {
        $this->assertTrue(in_iterable($value, $iterable));
    }

    /** @dataProvider provideNotPresentValuesInIterables */
    public function testThatValueIsNotInIterable($value, $iterable): void
    {
        $this->assertFalse(in_iterable($value, $iterable));
    }

    /** @dataProvider provideIterablesWithUniqueAndDuplicatedValues */
    public function testThatDuplicatedIterableValuesAreRemoved(array $expected, iterable $iterable): void
    {
        $uniqueIterable = $this->iterableToArray(iterable_unique($iterable));

        $this->assertCount(count($expected), $uniqueIterable);
        $this->assertEmpty(array_udiff_assoc($expected, $uniqueIterable, function ($a, $b) {
            return equals($a, $b) ? 0 : ($a > $b ? 1 : -1);
        }));
    }

    /** @dataProvider provideIterablesWithExistingKeys */
    public function testThatKeyIsReturnedFromIterable($expectedKey, $value, iterable $iterable): void
    {
        $this->assertSame($expectedKey, iterable_search($value, $iterable));
    }

    /** @dataProvider provideIterablesWithNonExistingKeys */
    public function testThatKeyIsNotReturnedFromIterable($value, iterable $iterable): void
    {
        $this->assertFalse(iterable_search($value, $iterable));
    }

    /** @dataProvider provideEqualArrays */
    public function testThatArraysEqual(array $a, array $b): void
    {
        $this->assertTrue(array_equals($a, $b));
    }

    /** @dataProvider provideNotEqualArrays */
    public function testThatArraysNotEqual(array $a, array $b): void
    {
        $this->assertFalse(array_equals($a, $b));
    }

    public function provideEqualVariables(): iterable
    {
        yield [null, null];
        yield [true, true];
        yield [1.23, 1.23];
        yield [123, 123];
        yield [[1, 2, 3], [1, 2, 3]];
        yield [new SameEquatable(123), new SameEquatable(123)];
        yield [new VaryEquatable(123), 123];
    }

    public function provideNotEqualVariables(): iterable
    {
        yield [true, false];
        yield [null, false];
        yield ['', null];
        yield [1.23, 2.34];
        yield [1.23, 123];
        yield [123, 234];
        yield [[1, 2, 3], [3, 2, 1]];
        yield [new SameEquatable(123), null];
        yield [new SameEquatable(123), new VaryEquatable(123)];
        yield [new VaryEquatable(123), 234];
    }

    public function providePresentValuesInIterables(): iterable
    {
        yield [null, [null]];
        yield [123, [234, 123]];
        yield [1.23, [1.23, 0.23]];
        yield [true, [false, true]];
        yield [[1, 2], [[2, 3], [1, 2]]];
        yield ['foo', ['bar', 'foo']];
        yield [new SameEquatable(123), [new SameEquatable(234), new SameEquatable(123)]];
    }

    public function provideNotPresentValuesInIterables(): iterable
    {
        yield [null, []];
        yield [true, [false]];
        yield [123, [234, 345]];
        yield [[1, 2], [[2, 3], [3, 4]]];
        yield ['foo', ['bar', 'zip']];
        yield [new SameEquatable(123), [new SameEquatable(234), new SameEquatable(345)]];
    }

    public function provideIterablesWithUniqueAndDuplicatedValues(): iterable
    {
        yield [[], []];
        yield [[null], [null, null]];
        yield [[1, 2, 3 => 3], [1, 2, 2, 3, 2]];
        yield [['foo'], ['foo', 'foo']];
        yield [['foo', ''], ['foo', '']];
        yield [[true, false], [true, false, true]];
        yield [[1.23, 2.34], [1.23, 2.34, 1.23]];
        yield [
            [new SameEquatable(123), new SameEquatable(345), 3 => new SameEquatable(321)],
            [new SameEquatable(123), new SameEquatable(345), new SameEquatable(123), new SameEquatable(321)]
        ];
    }

    public function provideIterablesWithExistingKeys(): iterable
    {
        yield ['bar', 2, [
            'foo' => 1,
            'bar' => 2,
            'zoo' => 2
        ]];
        yield ['bar', new SameEquatable(2), [
            'foo' => 1,
            'bar' => new SameEquatable(2),
            'zoo' => new SameEquatable(2)
        ]];
        yield ['zoo', new VaryEquatable(2), [
            'foo' => 1,
            'bar' => new SameEquatable(2),
            'zoo' => 2
        ]];
    }

    public function provideIterablesWithNonExistingKeys(): iterable
    {
        yield [0, []];
        yield [0, [
            'foo' => 1,
            'bar' => 2,
            'zoo' => 2
        ]];
        yield [new SameEquatable(1), [
            'foo' => 1,
            'bar' => new SameEquatable(2),
            'zoo' => new SameEquatable(2)
        ]];
    }

    public function provideEqualArrays(): iterable
    {
        yield [[], []];
        yield [[null], [null]];
        yield [[1 => 'foo', 0 => 'bar'], ['bar', 'foo']];
        yield [[1.23, 2.34], [1.23, 2.34]];
        yield [[new SameEquatable(123), new SameEquatable(234)], [new SameEquatable(123), new SameEquatable(234)]];
        yield [[new VaryEquatable(123)], [123]];
    }

    public function provideNotEqualArrays(): iterable
    {
        yield [[], [null]];
        yield [[false], [null]];
        yield [['foo'], []];
        yield [['foo'], ['foo', 'bar']];
        yield [['foo', 'bar'], ['foo']];
        yield [['foo', 'bar'], ['bar', 'foo']];
        yield [[new SameEquatable(123)], [new SameEquatable(234)]];
        yield [[123], [new VaryEquatable(123)]];
    }

    private function iterableToArray(iterable $iterable): array
    {
        $arr = [];
        foreach ($iterable as $key => $value) {
            $arr[$key] = $value;
        }

        return $arr;
    }
}

/** @implements Equatable<SameEquatable> */
final class SameEquatable implements Equatable
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function equals(self $other): bool
    {
        return $this->value == $other->value;
    }
}

/** @implements Equatable<int> */
final class VaryEquatable implements Equatable
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function equals(int $other): bool
    {
        return $this->value == $other;
    }
}

<?php

namespace Jungi\Common\Tests;

use Jungi\Common\Comparator;
use Jungi\Common\Equatable;
use PHPUnit\Framework\TestCase;

/**
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ComparatorTest extends TestCase
{
    /** @dataProvider provideEqualVariables */
    public function testThatTwoVariablesEqual($a, $b): void
    {
        $this->assertTrue(Comparator::equals($a, $b));
        $this->assertTrue(Comparator::equals($b, $a));
    }

    /** @dataProvider provideNotEqualVariables */
    public function testThatTwoVariablesNotEqual($a, $b): void
    {
        $this->assertFalse(Comparator::equals($a, $b));
        $this->assertFalse(Comparator::equals($b, $a));
    }

    public function provideEqualVariables(): iterable
    {
        yield [null, null];
        yield [true, true];
        yield [1.23, 1.23];
        yield [123, 123];
        yield [[1, 2, 3], [1, 2, 3]];
        yield [new DummyEquatable(123), new DummyEquatable(123)];
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
        yield [new DummyEquatable(123), null];
        yield [new DummyEquatable(123), new AnotherDummyEquatable(123)];
    }
}

trait DummyEquatableTrait
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function equals(self $object): bool
    {
        return $this->value == $object->value;
    }
}

final class DummyEquatable implements Equatable
{
    use DummyEquatableTrait;
}

final class AnotherDummyEquatable implements Equatable
{
    use DummyEquatableTrait;
}

<?php

namespace Jungi\Common\Tests;

use Jungi\Common\Option;
use PHPUnit\Framework\TestCase;

/**
 * @author Piotr Kugla <piku235@gmail.com>
 */
class OptionTest extends TestCase
{
    public function testSomeOption(): void
    {
        $option = Option::Some(123);

        $this->assertTrue($option->isSome());
        $this->assertFalse($option->isNone());
        $this->assertEquals(123, $option->get());
        $this->assertEquals(123, $option->getOr(234));
        $this->assertEquals(123, $option->getOrNull());
        $this->assertEquals(123, $option->getOrElse(fn() => 234));
    }

    public function testNoneOption(): void
    {
        $option = Option::None();

        $this->assertFalse($option->isSome());
        $this->assertTrue($option->isNone());
        $this->assertEquals(234, $option->getOr(234));
        $this->assertNull($option->getOrNull());
        $this->assertEquals(234, $option->getOrElse(fn() => 234));
    }

    /** @dataProvider provideEqualOptions */
    public function testThatTwoOptionsEqual(Option $op1, Option $op2): void
    {
        $this->assertTrue($op1->equals($op2));
        $this->assertTrue($op2->equals($op1));
    }

    public function testThatTwoOptionsNotEqual(): void
    {
        $op1 = Option::Some(123);
        $op2 = Option::None();

        $this->assertFalse($op1->equals($op2));
        $this->assertFalse($op2->equals($op1));
    }

    public function testCombiningOptionsByAndThen(): void
    {
        $option = Option::Some(2)
            ->andThen(fn($value) => 3 * $value)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($option->isSome());
        $this->assertEquals(7, $option->get());
    }

    public function testCombiningOptionsByAndThenTo(): void
    {
        $op1 = Option::Some(2);
        $op2 = $op1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($op2->isSome());
        $this->assertEquals(8, $op2->get());

        $op1 = Option::None();
        $op2 = $op1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($op2->isNone());

        $op1 = Option::Some(2);
        $op2 = $op1
            ->andThenTo(fn($value) => Option::None())
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($op2->isNone());

        $op1 = Option::Some(2);
        $op2 = $op1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo(fn($value) => Option::None());

        $this->assertTrue($op2->isNone());
    }

    public function testThatNoneOptionFailsOnGet(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "None" value.');

        $option = Option::None();
        $option->get();
    }

    public function testSomeOptionAsOkOrErr(): void
    {
        $option = Option::Some(123);
        $result = $option->asOkOr('err');

        $this->assertTrue($result->isOk());

        $option = Option::None();
        $result = $option->asOkOr('err');

        $this->assertTrue($result->isErr());
        $this->assertEquals('err', $result->getErr());
    }

    public function provideEqualOptions(): iterable
    {
        yield [Option::Some(123), Option::Some(123)];
        yield [Option::None(), Option::None()];
    }

    public static function multiply(int $value): Option
    {
        return Some(2 * $value);
    }
}

<?php

namespace Jungi\Common\Tests;

use Jungi\Common\Result;
use PHPUnit\Framework\TestCase;

/**
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ResultTest extends TestCase
{
    public function testOkResult(): void
    {
        $result = Result::ok(123);

        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isErr());
        $this->assertEquals(123, $result->get());
        $this->assertEquals(123, $result->getOr(234));
        $this->assertEquals(123, $result->getOrNull());
        $this->assertEquals(123, $result->getOrElse(fn($val) => 2 * $val));
    }

    public function testErrResult(): void
    {
        $result = Result::err(123);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isErr());
        $this->assertEquals(123, $result->getErr());
        $this->assertEquals(234, $result->getOr(234));
        $this->assertNull($result->getOrNull());
        $this->assertEquals(246, $result->getOrElse(fn($val) => 2 * $val));
    }

    /** @dataProvider provideEqualResults */
    public function testThatTwoResultsEqual(Result $r1, Result $r2): void
    {
        $this->assertTrue($r1->equals($r2));
        $this->assertTrue($r2->equals($r1));
    }

    /** @dataProvider provideNotEqualResults */
    public function testThatTwoResultsNotEqual(Result $r1, Result $r2): void
    {
        $this->assertFalse($r1->equals($r2));
        $this->assertFalse($r2->equals($r1));
    }

    public function testCombiningResultsByAndThen(): void
    {
        $result = Result::ok(2)
            ->andThen(fn($value) => 3 * $value)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(7, $result->get());

        $result = Result::err(2)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(2, $result->getErr());
    }

    public function testCombiningResultsByAndThenTo(): void
    {
        $r1 = Result::ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(8, $r2->get());

        $r1 = Result::err(3);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(3, $r2->getErr());

        $r1 = Result::ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'err'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(2, $r2->getErr());

        $r1 = Result::ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(4, $r2->getErr());
    }

    public function testCombiningResultsByOrElse(): void
    {
        $result = Result::err(2)
            ->orElse(fn($value) => 3 * $value)
            ->orElse(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(7, $result->getErr());

        $result = Result::ok(2)
            ->orElse(fn($value) => 2 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(2, $result->get());
    }

    public function testCombiningResultsByOrElseTo(): void
    {
        $r1 = Result::ok(2);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'multiply'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(2, $r2->get());

        $r1 = Result::err(3);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'multiply'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(6, $r2->get());

        $r1 = Result::err(4);
        $r2 = $r1
            ->orElseTo(fn($value) => Result::err($value - 2))
            ->orElseTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(4, $r2->get());

        $r1 = Result::err(3);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'err'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(3, $r2->getErr());
    }

    public function testThatResultMapsOrElse(): void
    {
        $okFn = fn($value) => 3 * $value;
        $errFn = fn($value) => $value / 2;

        $this->assertEquals(6, Result::ok(2)->mapOrElse($errFn, $okFn));
        $this->assertEquals(3, Result::err(6)->mapOrElse($errFn, $okFn));
    }

    public function testThatOkResultFailsOnGetErr(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "ok" value.');

        $result = Result::ok(123);
        $result->getErr();
    }

    public function testThatErrResultFailsOnGet(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "err" value.');

        $result = Result::err(123);
        $result->get();
    }

    public function testResultAsOk(): void
    {
        $result = Result::ok(123);
        $option = $result->asOk();

        $this->assertTrue($option->isSome());
        $this->assertEquals(123, $option->get());

        $result = Result::err(123);
        $option = $result->asOk();

        $this->assertTrue($option->isNone());
    }

    public function testResultAsErr(): void
    {
        $result = Result::ok(123);
        $option = $result->asErr();

        $this->assertTrue($option->isNone());

        $result = Result::err(123);
        $option = $result->asErr();

        $this->assertTrue($option->isSome());
        $this->assertEquals(123, $option->get());
    }

    public function provideEqualResults(): iterable
    {
        yield [Result::ok(123), Result::ok(123)];
        yield [Result::err(234), Result::err(234)];
    }

    public function provideNotEqualResults(): iterable
    {
        yield [Result::ok(123), Result::err(123)];
        yield [Result::ok(123), Result::ok(234)];
        yield [Result::err(123), Result::err(234)];
    }

    public static function multiply(int $value): Result
    {
        return Result::ok(2 * $value);
    }

    public static function err($value): Result
    {
        return Result::err($value);
    }
}

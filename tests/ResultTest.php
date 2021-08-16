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
        $result = Result::Ok(123);

        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isErr());
        $this->assertEquals(123, $result->get());
        $this->assertEquals(123, $result->getOr(null));
        $this->assertEquals(123, $result->getOrElse(fn($val) => 2 * $val));
    }

    public function testErrResult(): void
    {
        $result = Result::Err(123);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isErr());
        $this->assertNull($result->getOr(null));
        $this->assertEquals(123, $result->getErr());
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
        $result = Result::Ok(2)
            ->andThen(fn($value) => 3 * $value)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(7, $result->get());

        $result = Result::Err(2)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(2, $result->getErr());
    }

    public function testCombiningResultsByAndThenTo(): void
    {
        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(8, $r2->get());

        $r1 = Result::Err(3);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(3, $r2->getErr());

        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'err'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(2, $r2->getErr());

        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(4, $r2->getErr());
    }

    public function testCombiningResultsByOrElse(): void
    {
        $result = Result::Err(2)
            ->orElse(fn($value) => 3 * $value)
            ->orElse(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(7, $result->getErr());

        $result = Result::Ok(2)
            ->orElse(fn($value) => 2 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(2, $result->get());
    }

    public function testCombiningResultsByOrElseTo(): void
    {
        $r1 = Result::Ok(2);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'multiply'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(2, $r2->get());

        $r1 = Result::Err(3);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'multiply'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(6, $r2->get());

        $r1 = Result::Err(4);
        $r2 = $r1
            ->orElseTo(fn($value) => Result::Err($value - 2))
            ->orElseTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(4, $r2->get());

        $r1 = Result::Err(3);
        $r2 = $r1
            ->orElseTo([__CLASS__, 'err'])
            ->orElseTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(3, $r2->getErr());
    }

    public function testThatOkResultFailsOnUnwrapErr(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "Ok" value.');

        $result = Result::Ok(123);
        $result->getErr();
    }

    public function testThatErrResultFailsOnUnwrap(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "Err" value.');

        $result = Result::Err(123);
        $result->get();
    }

    public function testResultAsOk(): void
    {
        $result = Result::Ok(123);
        $option = $result->asOk();

        $this->assertTrue($option->isSome());
        $this->assertEquals(123, $option->get());

        $result = Result::Err(123);
        $option = $result->asOk();

        $this->assertTrue($option->isNone());
    }

    public function testResultAsErr(): void
    {
        $result = Result::Ok(123);
        $option = $result->asErr();

        $this->assertTrue($option->isNone());

        $result = Result::Err(123);
        $option = $result->asErr();

        $this->assertTrue($option->isSome());
        $this->assertEquals(123, $option->get());
    }

    public function provideEqualResults(): iterable
    {
        yield [Result::Ok(123), Result::Ok(123)];
        yield [Result::Err(234), Result::Err(234)];
    }

    public function provideNotEqualResults(): iterable
    {
        yield [Result::Ok(123), Result::Err(123)];
        yield [Result::Ok(123), Result::Ok(234)];
        yield [Result::Err(123), Result::Err(234)];
    }

    public static function multiply(int $value): Result
    {
        return Result::Ok(2 * $value);
    }

    public static function err($value): Result
    {
        return Result::Err($value);
    }
}

<?php

namespace Jungi\Core\Tests;

use Jungi\Core\Result;
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
        $this->assertEquals(123, $result->unwrap());
        $this->assertEquals(123, $result->unwrapOr(null));
    }

    public function testErrResult(): void
    {
        $result = Result::Err(123);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isErr());
        $this->assertNull($result->unwrapOr(null));
        $this->assertEquals(123, $result->unwrapErr());
    }

    public function testCombiningResultsByAndThen(): void
    {
        $result = Result::Ok(2)
            ->andThen(fn($value) => 3 * $value)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(7, $result->unwrap());

        $result = Result::Err(2)
            ->andThen(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(2, $result->unwrapErr());
    }

    public function testCombiningResultsByAndThenTo(): void
    {
        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isOk());
        $this->assertEquals(8, $r2->unwrap());

        $r1 = Result::Err(3);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(3, $r2->unwrapErr());

        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'err'])
            ->andThenTo([__CLASS__, 'multiply']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(2, $r2->unwrapErr());

        $r1 = Result::Ok(2);
        $r2 = $r1
            ->andThenTo([__CLASS__, 'multiply'])
            ->andThenTo([__CLASS__, 'err']);

        $this->assertTrue($r2->isErr());
        $this->assertEquals(4, $r2->unwrapErr());
    }

    public function testCombiningResultsByOrElse(): void
    {
        $result = Result::Err(2)
            ->orElse(fn($value) => 3 * $value)
            ->orElse(fn($value) => 1 + $value);

        $this->assertTrue($result->isErr());
        $this->assertEquals(7, $result->unwrapErr());

        $result = Result::Ok(2)
            ->orElse(fn($value) => 2 + $value);

        $this->assertTrue($result->isOk());
        $this->assertEquals(2, $result->unwrap());
    }

    public function testThatOkResultFailsOnUnwrapErr(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "Ok" value.');

        $result = Result::Ok(123);
        $result->unwrapErr();
    }

    public function testThatErrResultFailsOnUnwrap(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "Err" value.');

        $result = Result::Err(123);
        $result->unwrap();
    }

    public function testResultAsOk(): void
    {
        $result = Result::Ok(123);
        $option = $result->asOk();

        $this->assertTrue($option->isSome());
        $this->assertEquals(123, $option->unwrap());

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
        $this->assertEquals(123, $option->unwrap());
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

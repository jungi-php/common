<?php

namespace Jungi\Common\Tests;

use Jungi\Common\Equatable;
use Jungi\Common\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    private const int ERR_TEST = 1;

    public function testThatExpectedValueIsAvailable(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::ok("foo");

        $this->assertTrue($r->isOk());
        $this->assertEquals("foo", $r->value);
        $this->assertEquals("foo", $r());
    }

    public function testThatThrowsOnValue(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::ok("foo");

        $this->expectException(\LogicException::class);

        $r->error;
    }

    public function testThatErrorIsAvailable(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::error(self::ERR_TEST);

        $this->assertFalse($r->isOk());
        $this->assertEquals(self::ERR_TEST, $r->error);
    }

    public function testThatThrowsOnError(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::error(self::ERR_TEST);

        $this->expectException(\LogicException::class);

        $r->value;
    }

    public function testThatResultEqualsAndDoesNotEqual(): void
    {
        $this->assertTrue(Result::ok("foo")->equals(Result::ok("foo")));
        $this->assertTrue(Result::ok(new Foo("foo"))->equals(Result::ok(new Foo("foo"))));
        $this->assertTrue(Result::error(self::ERR_TEST)->equals(Result::error(self::ERR_TEST)));
        $this->assertTrue(Result::error(new Foo("foo"))->equals(Result::error(new Foo("foo"))));
        $this->assertFalse(Result::ok("foo")->equals(Result::error("foo")));
        $this->assertFalse(Result::error("foo")->equals(Result::ok("foo")));
        $this->assertFalse(Result::error("foo")->equals(Result::ok("foo")));
    }

    public function testThatDefaultValueIsReturned(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::error(self::ERR_TEST);

        $this->assertNull($r->valueOr(null));
    }

    public function testThatDefaultValueIsNotReturned(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::ok("foo");

        $this->assertEquals("foo", $r->valueOr("default"));
    }

    public function testThatValueIsMapped(): void
    {
        /** @var Result<double, int> $r */
        $r = Result::ok("foo")->map(fn ($value) => 1.23);

        $this->assertTrue($r->isOk());
        $this->assertEquals(1.23, $r->value);
    }

    public function testThatValueIsNotMapped(): void
    {
        /** @var Result<string, int> $r */
        $r = Result::error(self::ERR_TEST)->map(fn($value) => 1.23);

        $this->assertFalse($r->isOk());
        $this->assertEquals(self::ERR_TEST, $r->error);
    }

    public function testThatErrorIsMapped(): void
    {
        /** @var Result<string, string> $r */
        $r = Result::error(self::ERR_TEST)->mapError(fn ($value) => "err");

        $this->assertFalse($r->isOk());
        $this->assertEquals("err", $r->error);
    }

    public function testThatErrorIsNotMapped(): void
    {
        /** @var Result<string, string> $r */
        $r = Result::ok("foo")->mapError(fn ($value) => "err");

        $this->assertTrue($r->isOk());
        $this->assertEquals("foo", $r->value);
    }
}

final class Foo implements Equatable
{
    public function __construct(public readonly string $value) {}

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

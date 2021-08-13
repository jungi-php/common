<?php

namespace Jungi\Core\Tests;

use Jungi\Core\Option;
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
        $this->assertEquals(123, $option->unwrap());
        $this->assertEquals(123, $option->unwrapOr(null));
    }

    public function testNoneOption(): void
    {
        $option = Option::None();

        $this->assertFalse($option->isSome());
        $this->assertTrue($option->isNone());
        $this->assertNull($option->unwrapOr(null));
    }

    public function testThatNoneOptionFailsOnUnwrap(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Called on an "None" value.');

        $option = Option::None();
        $option->unwrap();
    }

    public function testSomeOptionAsOkOrErr(): void
    {
        $option = Option::Some(123);
        $result = $option->asOkOr('err');

        $this->assertTrue($result->isOk());

        $option = Option::None();
        $result = $option->asOkOr('err');

        $this->assertTrue($result->isErr());
        $this->assertEquals('err', $result->unwrapErr());
    }
}

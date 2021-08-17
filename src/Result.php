<?php

namespace Jungi\Common;

/**
 * It represents either a success (Ok) or a failure (Err) result.
 * Instead of throwing exceptions, a failure is an expected, recoverable error.
 *
 * @template T
 * @template E
 *
 * @implements Equatable<Result<T, E>>
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class Result implements Equatable
{
    /**
     * Result with an ok value.
     *
     * @param T $value
     *
     * @see Ok() A shorthand version
     *
     * @return Result<T, E>
     */
    public static function Ok($value = null): self
    {
        return new Ok($value);
    }

    /**
     * Result with an error value.
     *
     * @param E $value
     *
     * @see Err() A shorthand version
     *
     * @return Result<T, E>
     */
    public static function Err($value = null): self
    {
        return new Err($value);
    }

    /**
     * Returns true if the Result is ok.
     *
     * @return bool
     */
    abstract public function isOk(): bool;

    /**
     * Returns true if the Result is err.
     *
     * @return bool
     */
    abstract public function isErr(): bool;

    /**
     * Maps the ok value by using the provided callback
     * and returns new result, leaving the error value untouched.
     *
     * Example:
     *
     * <code>
     *   function calc(int $value): int { return 2 * $value; }
     *
     *   Result::Ok(2)->andThen('calc').get()     // ok: 4
     *   Result::Err(2)->andThen('calc').getErr() // err: 2
     * </code>
     *
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Result<U, E>
     */
    abstract public function andThen(callable $fn): self;

    /**
     * Maps the ok value by using the provided callback which
     * returns new result that can be either ok or err.
     *
     * Example:
     *
     * <code>
     *   function calc(int $value): Result { return Ok(2 * $value); }
     *   function err($value): Result { return Err($value) }
     *
     *   Result::Ok(2)->andThenTo('calc')->andThenTo('calc').get()     // ok: 8
     *   Result::Ok(2)->andThenTo('calc')->andThenTo('err').getErr()   // err: 4
     *   Result::Ok(2)->andThenTo('err')->andThenTo('calc').getErr()   // err: 2
     *   Result::Err(2)->andThenTo('calc')->andThenTo('calc').getErr() // err: 2
     * </code>
     *
     * @template U
     * @template R
     *
     * @param callable(T): Result<U, R> $fn
     *
     * @return Result<U, R>
     */
    abstract public function andThenTo(callable $fn): self;

    /**
     * Maps the error value by using the provided callback
     * and returns new result, leaving the ok value untouched.
     *
     * Example:
     *
     * <code>
     *   function calc(int $value): { return 2 * $value; }
     *
     *   Result::Ok(2)->orElse('calc').get()     // ok: 2
     *   Result::Err(2)->orElse('calc').getErr() // err: 4
     * </code>
     *
     * @template R
     *
     * @param callable(E): R $fn
     *
     * @return Result<T, R>
     */
    abstract public function orElse(callable $fn): self;

    /**
     * Maps the err value by using the provided callback which
     * returns new result that can be either ok or err.
     *
     * Example:
     *
     * <code>
     *   function calc(int $value): Result { return Ok(2 * $value); }
     *   function err($value): Result { return Err($value) }
     *
     *   Result::Ok(2)->orElseTo('calc')->orElseTo('err').get()     // ok: 2
     *   Result::Err(2)->orElseTo('calc')->orElseTo('err').get()    // ok: 4
     *   Result::Err(2)->orElseTo('err')->orElseTo('calc').get()    // ok: 4
     *   Result::Err(2)->orElseTo('err')->orElseTo('err').getErr()  // err: 2
     * </code>
     *
     * @template U
     * @template R
     *
     * @param callable(E): Result<U, R> $fn
     *
     * @return Result<U, R>
     */
    abstract public function orElseTo(callable $fn): self;

    /**
     * Returns the ok value.
     *
     * @return T
     *
     * @throws \LogicException If Result is err
     */
    abstract public function get();

    /**
     * Returns the ok value or the provided value on err.
     *
     * @param T $value
     *
     * @return T
     */
    abstract public function getOr($value);

    /**
     * Returns the ok value or a value returned
     * by the provided callback on err.
     *
     * @param callable(E): T $fn
     *
     * @return T
     */
    abstract public function getOrElse(callable $fn);

    /**
     * Returns the error value.
     *
     * @return E
     *
     * @throws \LogicException If Result is ok
     */
    abstract public function getErr();

    /**
     * Returns an Option::Some(T) where T is the ok value.
     *
     * @return Option
     */
    abstract public function asOk(): Option;

    /**
     * Returns an Option::Some(E) where E is the error value.
     *
     * @return Option
     */
    abstract public function asErr(): Option;
}

/**
 * @template T
 *
 * @internal
 * @see Result::Ok()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class Ok extends Result
{
    /** @var T */
    private $value;

    /**
     * @param T $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isOk(): bool
    {
        return true;
    }

    public function isErr(): bool
    {
        return false;
    }

    public function equals(Result $result): bool
    {
        return $result instanceof self && equals($this->value, $result->value);
    }

    public function andThen(callable $fn): Result
    {
        return new self($fn($this->value));
    }

    public function andThenTo(callable $fn): Result
    {
        return $fn($this->value);
    }

    public function orElse(callable $fn): Result
    {
        return $this;
    }

    public function orElseTo(callable $fn): Result
    {
        return $this;
    }

    public function get()
    {
        return $this->value;
    }

    public function getOr($value)
    {
        return $this->value;
    }

    public function getErr()
    {
        throw new \LogicException('Called on an "Ok" value.');
    }

    public function getOrElse(callable $fn)
    {
        return $this->value;
    }

    public function asOk(): Option
    {
        return Option::Some($this->value);
    }

    public function asErr(): Option
    {
        return Option::None();
    }
}

/**
 * @template E
 *
 * @internal
 * @see Result::Err()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class Err extends Result
{
    /** @var E */
    private $value;

    /**
     * @param E $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isOk(): bool
    {
        return false;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function equals(Result $result): bool
    {
        return $result instanceof self && equals($this->value, $result->value);
    }

    public function andThen(callable $fn): Result
    {
        return $this;
    }

    public function andThenTo(callable $fn): Result
    {
        return $this;
    }

    public function orElse(callable $fn): Result
    {
        return new self($fn($this->value));
    }

    public function orElseTo(callable $fn): Result
    {
        return $fn($this->value);
    }

    public function get()
    {
        throw new \LogicException('Called on an "Err" value.');
    }

    public function getOr($value)
    {
        return $value;
    }

    public function getOrElse(callable $fn)
    {
        return $fn($this->value);
    }

    public function getErr()
    {
        return $this->value;
    }

    public function asOk(): Option
    {
        return Option::None();
    }

    public function asErr(): Option
    {
        return Option::Some($this->value);
    }
}

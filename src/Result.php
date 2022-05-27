<?php

namespace Jungi\Common;

/**
 * It represents either a success (ok) or a failure (err) result.
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
     * Example:
     *
     * <code>
     *   Result::ok(2);
     *   ok(2); // alias
     * </code>
     *
     * @template T
     * @template E
     *
     * @param T $value
     *
     * @see ok() An alias
     *
     * @return Result<T, E>
     */
    public static function ok($value = null): self
    {
        return new Ok($value);
    }

    /**
     * Result with an error value.
     *
     * Example:
     *
     * <code>
     *   Result::err(3);
     *   err(3); // alias
     * </code>
     *
     * @template T
     * @template E
     *
     * @param E $value
     *
     * @see err() An alias
     *
     * @return Result<T, E>
     */
    public static function err($value = null): self
    {
        return new Err($value);
    }

    /**
     * Returns true if the result is ok.
     *
     * Example:
     *
     * <code>
     *   assert(true === ok(2)->isOk());
     *   assert(false === err(2)->isOk());
     * </code>
     *
     * @return bool
     */
    abstract public function isOk(): bool;

    /**
     * Returns true if the result is err.
     *
     * Example:
     *
     * <code>
     *   assert(true === err(3)->isErr());
     *   assert(false === ok(3)->isErr());
     * </code>
     *
     * @return bool
     */
    abstract public function isErr(): bool;

    /**
     * Returns true if this result equals another result.
     *
     * Example:
     *
     * <code>
     *   assert(true === ok(2)->equals(ok(2)));
     *   assert(true === err(3)->equals(err(3)));
     *   assert(false === ok(2)->equals(ok('2')));
     *   assert(false === ok(2)->equals(err(2)));
     * </code>
     *
     * @param Result<T, E> $other
     */
    abstract public function equals(self $other): bool;

    /**
     * Maps the ok value by using the provided callback
     * and returns a new result, leaving the error value untouched.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 2 * $value; }
     *
     *   assert(4 === ok(2)->andThen('mul')->get());
     *   assert(2 === err(2)->andThen('mul')->getErr());
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
     * returns a new result that can be either ok or err.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): Result { return ok(2 * $value); }
     *
     *   assert(8 === ok(2)->andThenTo('mul')->andThenTo('mul')->get());
     *   assert(4 === ok(2)->andThenTo('mul')->andThenTo('err')->getErr());
     *   assert(2 === ok(2)->andThenTo('err')->andThenTo('mul')->getErr());
     *   assert(2 === err(2)->andThenTo('mul')->andThenTo('mul')->getErr());
     * </code>
     *
     * @template U
     *
     * @param callable(T): Result<U, E> $fn
     *
     * @return Result<U, E>
     */
    abstract public function andThenTo(callable $fn): self;

    /**
     * Maps the error value by using the provided callback
     * and returns a new result, leaving the ok value untouched.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 2 * $value; }
     *
     *   assert(2 === ok(2)->orElse('mul')->get());
     *   assert(4 === err(2)->orElse('mul')->getErr());
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
     * Maps the error value by using the provided callback which
     * returns a new result that can be either ok or err.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): Result { return ok(2 * $value); }
     *
     *   assert(2 === ok(2)->orElseTo('mul')->orElseTo('err')->get());
     *   assert(4 === err(2)->orElseTo('mul')->orElseTo('err')->get());
     *   assert(4 === err(2)->orElseTo('err')->orElseTo('mul')->get());
     *   assert(2 === err(2)->orElseTo('err')->orElseTo('err')->getErr());
     * </code>
     *
     * @template R
     *
     * @param callable(E): Result<T, R> $fn
     *
     * @return Result<T, R>
     */
    abstract public function orElseTo(callable $fn): self;

    /**
     * If the result is ok, it maps its value using the provided
     * $okFn callback. Otherwise, returns the untouched result.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 3 * $value; }
     *
     *   assert(6 === ok(2)->mapOr(3, 'mul'));
     *   assert(3 === err(6)->mapOr(3, 'mul'));
     * </code>
     *
     * @template U
     *
     * @param U $value
     * @param callable(T): U $okFn
     *
     * @return U
     */
    abstract public function mapOr($value, callable $okFn);

    /**
     * If the result is ok, it maps its value using the provided
     * $okFn callback. Otherwise, if the result is err, it maps
     * its value using the provided $errFn callback.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 3 * $value; }
     *   function sum(int $value): int { return 2 + $value; }
     *
     *   assert(6 === ok(2)->mapOrElse('sum', 'mul'));
     *   assert(5 === err(3)->mapOrElse('sum', 'mul'));
     * </code>
     *
     * @template U
     *
     * @param callable(E): U $errFn
     * @param callable(T): U $okFn
     *
     * @return U
     */
    abstract public function mapOrElse(callable $errFn, callable $okFn);

    /**
     * Returns the ok value.
     *
     * Example:
     *
     * <code>
     *   assert(2 === ok(2)->get());
     *   err(1)->get(); // throws an exception
     * </code>
     *
     * @return T
     *
     * @throws \LogicException If result is err
     */
    abstract public function get();

    /**
     * Returns the ok value or the provided value on err.
     *
     * Example:
     *
     * <code>
     *   assert(2 === ok(2)->getOr(3));
     *   assert(3 === err(1)->getOr(3));
     * </code>
     *
     * @param T $value
     *
     * @return T
     */
    abstract public function getOr($value);

    /**
     * Returns the ok value or null on err.
     *
     * Example:
     *
     * <code>
     *   assert(2 === ok(2)->getOrNull());
     *   assert(null === err(1)->getOrNull());
     * </code>
     *
     * @return T|null
     */
    abstract public function getOrNull();

    /**
     * Returns the ok value or a value returned
     * by the provided callback on err.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 3 * $value; }
     *
     *   assert(2 === ok(2)->getOrElse('mul'));
     *   assert(9 === err(3)->getOrElse('mul'));
     * </code>
     * 
     * @param callable(E): T $fn
     *
     * @return T
     */
    abstract public function getOrElse(callable $fn);

    /**
     * Returns the error value.
     *
     * Example:
     *
     * <code>
     *   assert(3 === err(3)->getErr());
     *   ok(1)->getErr(); // throws an exception
     * </code>
     *
     * @return E
     *
     * @throws \LogicException If result is ok
     */
    abstract public function getErr();

    /**
     * Returns Option::Some(T) where T is the ok value.
     *
     * Example:
     *
     * <code>
     *   assert(2 === ok(2)->asOk()->get());
     *   assert(true === err(3)->asOk()->isNone());
     * </code>
     *
     * @return Option<T>
     */
    abstract public function asOk(): Option;

    /**
     * Returns Option::Some(E) where E is the error value.
     *
     * Example:
     *
     * <code>
     *   assert(3 === err(3)->asErr()->get());
     *   assert(true === ok(2)->asErr()->isNone());
     * </code>
     *
     * @return Option<E>
     */
    abstract public function asErr(): Option;
}

/**
 * @template T
 *
 * @internal
 * @see Result::ok()
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
    protected function __construct($value)
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

    public function equals(Result $other): bool
    {
        return $other instanceof self && equals($this->value, $other->value);
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

    public function mapOr($value, callable $okFn)
    {
        return $okFn($this->value);
    }

    public function mapOrElse(callable $errFn, callable $okFn)
    {
        return $okFn($this->value);
    }

    public function get()
    {
        return $this->value;
    }

    public function getOr($value)
    {
        return $this->value;
    }

    public function getOrNull()
    {
        return $this->value;
    }

    public function getOrElse(callable $fn)
    {
        return $this->value;
    }

    public function getErr()
    {
        throw new \LogicException('Called on an "ok" value.');
    }

    public function asOk(): Option
    {
        return Option::some($this->value);
    }

    public function asErr(): Option
    {
        return Option::none();
    }
}

/**
 * @template E
 *
 * @internal
 * @see Result::err()
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
    protected function __construct($value)
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

    public function equals(Result $other): bool
    {
        return $other instanceof self && equals($this->value, $other->value);
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

    public function mapOr($value, callable $okFn)
    {
        return $value;
    }

    public function mapOrElse(callable $errFn, callable $okFn)
    {
        return $errFn($this->value);
    }

    public function get()
    {
        throw new \LogicException('Called on an "err" value.');
    }

    public function getOr($value)
    {
        return $value;
    }

    public function getOrNull()
    {
        return null;
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
        return Option::none();
    }

    public function asErr(): Option
    {
        return Option::some($this->value);
    }
}

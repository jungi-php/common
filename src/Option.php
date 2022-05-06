<?php

namespace Jungi\Common;

/**
 * It represents either some value or none.
 * Instead of dealing with a null value, it allows for less error-prone operations.
 *
 * @template T
 * @implements Equatable<Option<T>>
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class Option implements Equatable
{
    /**
     * Option with some value.
     *
     * Example:
     *
     * <code>
     *   Option::some(2);
     *   some(2); // alias
     * </code>
     *
     * @param T $value
     *
     * @see some() An alias
     *
     * @return Option<T>
     */
    public static function some($value): self
    {
        return new Some($value);
    }

    /**
     * Option with no value.
     *
     * Example:
     *
     * <code>
     *   Option::none();
     *   none(); // alias
     * </code>
     *
     * @see none() An alias
     *
     * @return Option<T>
     */
    public static function none(): self
    {
        return new None();
    }

    /**
     * Returns true if the option is with some value.
     *
     * Example:
     *
     * <code>
     *   assert(true === some(2)->isSome());
     *   assert(false === none()->isSome());
     * </code>
     *
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * Returns true if the option is with no value.
     *
     * Example:
     *
     * <code>
     *   assert(true === none()->isNone());
     *   assert(false === some(2)->isNone());
     * </code>
     *
     * @return bool
     */
    abstract public function isNone(): bool;

    /**
     * Returns true if this option equals other option.
     *
     * Example:
     *
     * <code>
     *   assert(true === some(2)->equals(some(2)));
     *   assert(true === none()->equals(none()));
     *   assert(false === some(2)->equals(some('2')));
     *   assert(false === none()->equals(some(2)));
     * </code>
     *
     * @param Option<T> $other
     */
    abstract public function equals(self $other): bool;

    /**
     * Maps some value by using the provided callback
     * and returns a new option. Returns none if the option
     * is none.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 2 * $value; }
     *
     *   assert(4 === some(2)->andThen('mul')->get());
     *   assert(true === none()->andThen('mul')->isNone());
     * </code>
     *
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Option<U>
     */
    abstract public function andThen(callable $fn): self;

    /**
     * Maps some value by using the provided callback which
     * returns a new option that can be with some or no value.
     * Returns none if the option is none.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): Option { return some(2 * $value); }
     *
     *   assert(8 === some(2)->andThenTo('mul')->andThenTo('mul')->get());
     *   assert(true === some(2)->andThenTo('mul')->andThenTo('none')->isNone());
     *   assert(true === some(2)->andThenTo('none')->andThenTo('mul')->isNone());
     *   assert(true === none()->andThenTo('mul')->andThenTo('mul')->isNone());
     * </code>
     *
     * @template U
     *
     * @param callable(T): Option<U> $fn
     *
     * @return Option<U>
     */
    abstract public function andThenTo(callable $fn): self;

    /**
     * If the option is with no value, it calls the provided callback
     * to return a new option. Otherwise, it returns the untouched option.
     *
     * Example:
     *
     * <code>
     *   function def(): Option { return some(3); }
     *
     *   assert(3 === none()->orElseTo('none')->orElseTo('def')->get());
     *   assert(3 === none()->orElseTo('def')->orElseTo('none')->get());
     *   assert(5 === some(5)->orElseTo('def')->orElseTo('none')->get());
     *   assert(true === none()->orElseTo('none')->orElseTo('none')->isNone());
     * </code>
     *
     * @param callable(): Option<T> $fn
     *
     * @return Option<T>
     */
    abstract public function orElseTo(callable $fn): self;

    /**
     * If the option is with some value, it maps its value using
     * the provided $someFn callback, otherwise it returns
     * the provided value.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 2 * $value; }
     *
     *   assert(4 === some(2)->mapOrElse(3, 'mul'));
     *   assert(3 === none()->mapOrElse(3, 'mul'));
     * </code>
     *
     * @template U
     *
     * @param U $value
     * @param callable(T): U $someFn
     *
     * @return U
     */
    abstract public function mapOr($value, callable $someFn);

    /**
     * If the option is with some value, it maps its value using
     * the provided $someFn callback, otherwise it calls $noneFn callback.
     *
     * Example:
     *
     * <code>
     *   function mul(int $value): int { return 2 * $value; }
     *   function def(): int { return 3; }
     *
     *   assert(4 === some(2)->mapOrElse('def', 'mul'));
     *   assert(3 === none()->mapOrElse('def', 'mul'));
     * </code>
     *
     * @template U
     *
     * @param callable(): U $noneFn
     * @param callable(T): U $someFn
     *
     * @return U
     */
    abstract public function mapOrElse(callable $noneFn, callable $someFn);

    /**
     * Returns some value.
     *
     * Example:
     *
     * <code>
     *   assert(2 === some(2)->get());
     *   none()->get(); // throws an exception
     * </code>
     *
     * @return T
     *
     * @throws \LogicException If option is none
     */
    abstract public function get();

    /**
     * Returns some value or the provided value on none.
     *
     * Example:
     *
     * <code>
     *   assert(2 === some(2)->getOr(3));
     *   assert(3 === none()->getOr(3));
     * </code>
     *
     * @param T $value
     *
     * @return T
     */
    abstract public function getOr($value);

    /**
     * Returns some value or null on none.
     *
     * Example:
     *
     * <code>
     *   assert(2 === some(2)->getOrNull());
     *   assert(null === none()->getOrNull());
     * </code>
     *
     * @return T|null
     */
    abstract public function getOrNull();

    /**
     * Returns some value or a value returned
     * by the provided callback on none.
     *
     * @param callable(): T $fn
     *
     * @return T
     */
    abstract public function getOrElse(callable $fn);

    /**
     * Returns Result::Ok(T) where T is some value
     * or Result::Err(E) where E is an error value.
     *
     * Example:
     *
     * <code>
     *   assert(2 === some(2)->asOkOr(3)->get());
     *   assert(3 === none()->asOkOr(3)->getErr());
     * </code>
     *
     * @template E
     *
     * @param E $err
     *
     * @return Result<T, E>
     */
    abstract public function asOkOr($err): Result;
}

/**
 * @template T
 *
 * @internal
 * @see Option::some()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class Some extends Option
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

    public function isSome(): bool
    {
        return true;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function equals(Option $other): bool
    {
        return $other instanceof self && equals($this->value, $other->value);
    }

    public function andThen(callable $fn): Option
    {
        return new self($fn($this->value));
    }

    public function andThenTo(callable $fn): Option
    {
        return $fn($this->value);
    }

    public function orElseTo(callable $fn): Option
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

    public function asOkOr($err): Result
    {
        return Result::ok($this->value);
    }
}

/**
 * @internal
 * @see Option::none()
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
final class None extends Option
{
    protected function __construct() {}

    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function equals(Option $other): bool
    {
        return $other instanceof self;
    }

    public function andThen(callable $fn): Option
    {
        return $this;
    }

    public function andThenTo(callable $fn): Option
    {
        return $this;
    }

    public function orElseTo(callable $fn): Option
    {
        return $fn();
    }

    public function mapOr($value, callable $okFn)
    {
        return $value;
    }

    public function mapOrElse(callable $errFn, callable $okFn)
    {
        return $errFn();
    }

    public function get()
    {
        throw new \LogicException('Called on an "none" value.');
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
        return $fn();
    }

    public function asOkOr($err): Result
    {
        return Result::err($err);
    }
}

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
     * @see Some() A shorthand version
     *
     * @return Option<T>
     */
    public static function Some($value): self
    {
        return new Some($value);
    }

    /**
     * Option with no value.
     *
     * @see None() A shorthand version
     *
     * @return Option<T>
     */
    public static function None(): self
    {
        return new None();
    }

    /**
     * Returns true if the Option is with some value.
     *
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * Returns true if the Option is with no value.
     *
     * @return bool
     */
    abstract public function isNone(): bool;

    /**
     * Maps some value by using the provided callback
     * and returns new option. Returns none if the option
     * is none.
     *
     * Example:
     *
     * <code>
     *   Some(2)->andThen(fn($value) => 2 * $value).get() // ok: 4
     *   None()->andThen('calc').get()                    // none, exception on get()
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
     * returns new option that can be with some or no value.
     * Returns none if the option is none.
     *
     * Example:
     *
     * <code>
     *   function calc(int $value): Option { return Some(2 * $value); }
     *
     *   Some(2)->andThenTo('calc')->andThenTo('calc').get() // ok: 8
     *   Some(2)->andThenTo('calc')->andThenTo('None').get() // none, exception on get()
     *   Some(2)->andThenTo('None')->andThenTo('calc').get() // none, exception on get()
     *   None()->andThenTo('calc')->andThenTo('calc').get()  // none, exception on get()
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
     * Returns some value.
     *
     * @return T
     *
     * @throws \LogicException If Option is None
     */
    abstract public function get();

    /**
     * Returns some value or the provided value on none.
     *
     * @param T $value
     *
     * @return T
     */
    abstract public function getOr($value);

    /**
     * Returns some value or null on none.
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
     * Returns a Result::Ok(T) where T is some value
     * or a Result::Err(E) where E is an error value.
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
 * @see Option::Some()
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
        return Result::Ok($this->value);
    }
}

/**
 * @internal
 * @see Option::None()
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

    public function get()
    {
        throw new \LogicException('Called on an "None" value.');
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
        return Result::Err($err);
    }
}

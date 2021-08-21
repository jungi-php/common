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
     * @see none() An alias
     *
     * @return Option<T>
     */
    public static function none(): self
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
     *   some(2)->andThen(fn($value) => 2 * $value).get() // ok: 4
     *   none()->andThen('calc').get()                    // none, exception on get()
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
     *   function calc(int $value): Option { return some(2 * $value); }
     *
     *   some(2)->andThenTo('calc')->andThenTo('calc').get() // ok: 8
     *   some(2)->andThenTo('calc')->andThenTo('none').get() // none, exception on get()
     *   some(2)->andThenTo('none')->andThenTo('calc').get() // none, exception on get()
     *   none()->andThenTo('calc')->andThenTo('calc').get()  // none, exception on get()
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
     * @throws \LogicException If Option is none
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

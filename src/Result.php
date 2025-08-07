<?php

namespace Jungi\Common;

/**
 * Represents a value that can either be a success (containing a value of type T)
 * or a failure (containing an error of type E).
 *
 * @template T
 * @template E
 *
 * @implements Equatable<Result<T, E>>
 *
 * @final
 */
abstract class Result implements Equatable
{
    /**
     * @param T|null $value Null in case T of void
     *
     * @return Result<T, E>
     */
    public static function ok($value = null): self
    {
        return new Ok($value);
    }

    /**
     * @param E $value
     *
     * @return Result<T, E>
     */
    public static function error($value): self
    {
        return new Error($value);
    }

    /**
     * @var T
     *
     * @throws \LogicException If accessed when the result has error
     */
    protected(set) mixed $value;

    /**
     * @var E
     *
     * @throws \LogicException If accessed when the result has value
     */
    protected(set) mixed $error;

    /**
     * Dereferences the contained value.
     *
     * This is a shorthand for accessing the result's value directly `$this->value`.
     *
     * @example $r()->foo
     *
     * @return T The contained value
     *
     * @throws \LogicException If the result has error
     */
    public function __invoke()
    {
        return $this->value;
    }

    abstract public function isOk(): bool;

    /**
     * Returns the contained value, or the given default if the result is an error.
     *
     * @template U
     *
     * @param U $defaultValue
     *
     * @return T|U
     */
    abstract public function valueOr($defaultValue);

    /**
     * Returns a new result with the value mapped using the given callback.
     * If the result has error, it is returned unchanged.
     *
     * <code>
     *    function mul(int $value): int { return 3 * $value; }
     *
     *    assert(6 === Result::ok(2)->map('mul'));
     *    assert(2 === Result::error(2)->map('mul'));
     * </code>
     *
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Result<U, E>
     */
    abstract public function map(callable $fn): self;

    /**
     * Returns a new result with the error mapped using the given callback.
     * If the result has value, it is returned unchanged.
     *
     * <code>
     *     function mul(int $value): int { return 3 * $value; }
     *
     *     assert(2 === Result::ok(2)->mapError('mul'));
     *     assert(6 === Result::error(2)->mapError('mul'));
     * </code>
     *
     * @template U
     *
     * @param callable(E): U $fn
     *
     * @return Result<T, U>
     */
    abstract public function mapError(callable $fn): self;
}

/**
 * @internal Part of the implementation details, do not use outside
 */
final class Ok extends Result
{
    protected(set) mixed $value {
        get {
            return $this->value;
        }
    }

    protected(set) mixed $error {
        get => throw new \LogicException("Called on expected value.");
    }

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function equals(Result $other): bool
    {
        return $other instanceof self && equals($this->value, $other->value);
    }

    public function isOk(): bool
    {
        return true;
    }

    public function valueOr($defaultValue)
    {
        return $this->value;
    }

    public function map(callable $fn): self
    {
        return new self($fn($this->value));
    }

    public function mapError(callable $fn): self
    {
        return $this;
    }
}

/**
 * @internal Part of the implementation details, do not use outside
 */
final class Error extends Result
{
    protected(set) mixed $value {
        get => throw new \LogicException("Called on error.");
    }

    protected(set) mixed $error {
        get {
            return $this->error;
        }
    }

    public function __construct($error)
    {
        $this->error = $error;
    }

    public function equals(Result $other): bool
    {
        return $other instanceof self && equals($this->error, $other->error);
    }

    public function isOk(): bool
    {
        return false;
    }

    public function valueOr($defaultValue)
    {
        return $defaultValue;
    }

    public function map(callable $fn): self
    {
        return $this;
    }

    public function mapError(callable $fn): self
    {
        return new self($fn($this->error));
    }
}

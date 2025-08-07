<?php

namespace Jungi\Common;

/**
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
     * @param T $value
     *
     * @return self<T, E>
     */
    public static function ok($value): self
    {
        return new Ok($value);
    }

    /**
     * @param E $value
     *
     * @return self<T, E>
     */
    public static function error($value): self
    {
        return new Error($value);
    }

    /**
     * @var T
     *
     * @throws \LogicException
     */
    protected(set) mixed $value;

    /**
     * @var E
     *
     * @throws \LogicException
     */
    protected(set) mixed $error;

    /**
     * @return T
     */
    public function __invoke()
    {
        return $this->value;
    }

    abstract public function hasValue(): bool;

    /**
     * @template U
     *
     * @param U $other
     *
     * @return T|U
     */
    abstract public function valueOr($other);

    /**
     * @template U
     *
     * @param callable(T): U $fn
     *
     * @return Result<U, E>
     */
    abstract public function map(callable $fn): self;

    /**
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

    public function hasValue(): bool
    {
        return true;
    }

    public function valueOr($other)
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

    public function hasValue(): bool
    {
        return false;
    }

    public function valueOr($other)
    {
        return $other;
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

<?php

namespace Jungi\Core;

/**
 * @template T
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class Option
{
    public static function Some($value): self
    {
        return new Some($value);
    }

    public static function None(): self
    {
        return new None();
    }

    /**
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * @return bool
     */
    abstract public function isNone(): bool;

    /**
     * @return T
     */
    abstract public function expect(string $message);

    /**
     * @return T
     */
    abstract public function unwrap();

    /**
     * @param T $value
     *
     * @return T
     */
    abstract public function unwrapOr($value);

    /**
     * @template E
     * @param E $err
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
    public function __construct($value)
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

    /**
     * @return T
     */
    public function expect(string $message)
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        return $this->value;
    }

    /**
     * @param T $value
     *
     * @return T
     */
    public function unwrapOr($value)
    {
        return $this->value;
    }

    /**
     * @template E
     * @param E $err
     */
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
    public function isSome(): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function expect(string $message)
    {
        throw new \LogicException($message);
    }

    public function unwrap()
    {
        throw new \LogicException('Called on an "None" value.');
    }

    public function unwrapOr($value)
    {
        return $value;
    }

    /**
     * @template E
     * @param E $err
     */
    public function asOkOr($err): Result
    {
        return Result::Err($err);
    }
}

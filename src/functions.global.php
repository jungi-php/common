<?php

use Jungi\Common\Result;
use Jungi\Common\Option;

if (!function_exists('ok')) {
    /**
     * Result with an ok value.
     *
     * @see Result::ok()
     *
     * @template T
     * @template E
     *
     * @param T $value
     *
     * @return Result<T, E>
     */
    function ok($value = null): Result
    {
        return Result::ok($value);
    }
}

if (!function_exists('err')) {
    /**
     * Result with an error value.
     *
     * @see Result::err()
     *
     * @template T
     * @template E
     *
     * @param E $value
     *
     * @return Result<T, E>
     */
    function err($value = null): Result
    {
        return Result::err($value);
    }
}

if (!function_exists('some')) {
    /**
     * Option with some value.
     *
     * @see Option::some()
     *
     * @template T
     *
     * @param T $value
     */
    function some($value): Option
    {
        return Option::some($value);
    }
}

if (!function_exists('none')) {
    /**
     * Option with no value.
     *
     * @see Option::none()
     *
     * @return Option
     */
    function none(): Option
    {
        return Option::none();
    }
}

<?php

use Jungi\Common\Result;
use Jungi\Common\Option;

if (!function_exists('ok')) {
    /**
     * Result with an ok value.
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

if (!function_exists('Some')) {
    /**
     * @template T
     *
     * @param T $value
     */
    function Some($value): Option
    {
        return Option::Some($value);
    }
}

if (!function_exists('None')) {
    function None(): Option
    {
        return Option::None();
    }
}

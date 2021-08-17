<?php

namespace {
    use Jungi\Common\Result;
    use Jungi\Common\Option;

    if (!function_exists('Ok')) {
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
        function Ok($value = null): Result
        {
            return Result::Ok($value);
        }
    }

    if (!function_exists('Err')) {
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
        function Err($value = null): Result
        {
            return Result::Err($value);
        }
    }

    if (!function_exists('Some')) {
        /**
         * @template T
         *
         * @param T $value
         */
        function Some($value = null): Option
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
}

namespace Jungi\Common {
    /**
     * Returns true:
     *   if $a and $b implement Equatable, are of the same type, and are equal
     *   if $a and $b are equal, and of the same type "==="
     * otherwise false
     */
    function equals($a, $b): bool
    {
        if ($a instanceof Equatable && $b instanceof Equatable && $b instanceof $a) {
            return $a->equals($b);
        }

        return $a === $b;
    }
}

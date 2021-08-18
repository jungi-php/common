<?php

namespace Jungi\Common;

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

/**
 * Returns true if a value is present in an iterable,
 * otherwise false.
 */
function in_iterable($value, iterable $iterable): bool
{
    foreach ($iterable as $iteratedValue) {
        if (equals($value, $iteratedValue)) {
            return true;
        }
    }

    return false;
}

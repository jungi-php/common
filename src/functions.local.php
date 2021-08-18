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

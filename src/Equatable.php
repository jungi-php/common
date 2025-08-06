<?php

namespace Jungi\Common;

/**
 * Determines whether the object is equal to another value of type T.
 *
 * Due to PHP limitations, the method cannot be explicitly declared
 * inside the interface body. Implementing classes should specify
 * the concrete type for T when implementing this method,
 * ensuring both runtime and static type safety.
 *
 * @template T
 * @method bool equals(T $other)
 */
interface Equatable
{
    // public function equals($other): bool;
}

<?php

namespace Jungi\Common;

/**
 * Returns true:
 *   if $a implements Equatable and is equal with $b
 *   if $a and $b are equal, and of the same type "==="
 * otherwise false
 *
 * @param mixed $a
 * @param mixed $b
 */
function equals($a, $b): bool
{
    if ($a instanceof Equatable) {
        try {
            return $a->equals($b);
        } catch (\TypeError $e) {
            return false;
        }
    }

    return $a === $b;
}

/**
 * Returns true if a value is present in an iterable,
 * otherwise false.
 *
 * @param mixed $value
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

/**
 * Returns an iterable without duplicates.
 *
 * @param iterable $iterable
 */
function iterable_unique(iterable $iterable): iterable
{
    $seen = [];
    foreach ($iterable as $key => $value) {
        if (!in_iterable($value, $seen)) {
            $seen[] = $value;

            yield $key => $value;
        }
    }
}

/**
 * Returns the first key where the given value is equal.
 * If the value is not found, false is returned.
 *
 * @param mixed    $value
 * @param iterable $iterable
 *
 * @return mixed A key or false otherwise
 */
function iterable_search($value, iterable $iterable)
{
    foreach ($iterable as $key => $iteratedValue) {
        if (equals($value, $iteratedValue)) {
            return $key;
        }
    }

    return false;
}

/**
 * Returns true if both arrays have the same keys
 * and their values are equal.
 *
 * @param array $a
 * @param array $b
 *
 * @return bool
 */
function array_equals(array $a, array $b): bool
{
    if (count($a) !== count($b)) {
        return false;
    }

    foreach ($a as $key => $value) {
        if (!array_key_exists($key, $b) || !equals($value, $b[$key])) {
            return false;
        }
    }

    return true;
}

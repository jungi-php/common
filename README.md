# Jungi Common

![Build Status](https://github.com/piku235/jungi-common/actions/workflows/continuous-integration.yml/badge.svg)

A minimal library that goal is to provide essential basic functionality. It combines the advantages of functional
and object-oriented programming. All of this makes code easier to understand and less prone to errors.

Includes:
* `Result<T, E>`
* `Option<T>`
* `Equatable<T>`

## Quick insight

### Result

```php
function submit(): Result
{
    // ...
    if ($errors) {
        return Err($errors);
    }
    
    return Ok($id);
}

$v = submit()
    ->andThen(fn($id) => ['id' => $id])
    ->getOrElse(fn($errors) => formatErrors($errors));
```

### Option

```php
function getItem(): Option
{
    // ...
    if (!$item) {
        return None();
    }
    
    return Some($item);
}

$v = getItem()
    ->andThen(fn($item) => itemData($item))
    ->getOr(['code' => 'not found.']);
```

### Equatable

```php
/** @implements Equatable<FullName> */
class FullName implements Equatable
{
    private string $firstName;
    private string $lastName;
    
    // firstName and lastName are later initialized in the constructor

    public function equals(self $fullName): bool
    {
        return $this->firstName === $fullName->firstName && $this->lastName === $fullName->lastName;
    }
}

assert(true === (new FullName('David', 'Copper'))->equals(new FullName('David', 'Copper')));
assert(false === (new FullName('David', 'Copper'))->equals(new FullName('David', 'Eton')));
```

Furthermore, there are two functions that can much ease dealing with `Equatable<T>` types.

##### `equals($a, $b)`

```php
use function Jungi\Common\equals;

assert(true === equals(new FullName('David', 'Copper'), new FullName('David', 'Copper')));
assert(false === equals(new FullName('David', 'Copper'), new FullName('David', 'Aston')));
assert(false === equals(new FullName('David', 'Copper'), null));
```

##### `in_iterable($value, iterable $iterable)`

```php
use function Jungi\Common\in_iterable;

assert(true === in_iterable(new FullName('David', 'Copper'), [new FullName('David', 'Copper'), new FullName('James', 'Weston')]));
assert(false === in_iterable(new FullName('David', 'Copper'), []));
assert(false === in_iterable(new FullName('David', 'Copper'), [new FullName('David', 'Aston')]));
```

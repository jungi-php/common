# Jungi Common Library

A library that adds extra common features to PHP.

![Build Status](https://github.com/piku235/jungi-common/actions/workflows/continuous-integration.yml/badge.svg)

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

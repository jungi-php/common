# Jungi Core Library

A core library that adds extra basic features to PHP. Inspired by Rust core library.

![Build Status](https://github.com/piku235/jungi-core/actions/workflows/continuous-integration.yml/badge.svg)

**Includes:**
* Result type
* Option type

## Quick insight

```php
function submit(): Result
{
    // ...
    if ($errors) {
        return Err($errors);
    }
    
    return Ok();
}

$result = submit();
if ($result->isErr()) {
    $errors = $result->unwrapErr();
    // display errors back to the user
    exit(formatErrors($errors));
}

// ok
```

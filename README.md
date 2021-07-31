# Jungi Core Library

A core library that adds extra basic features to PHP. Inspired by Rust core library.

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

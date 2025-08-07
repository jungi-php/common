# Jungi Common

[![CI](https://github.com/jungi-php/common/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/jungi-php/common/actions/workflows/continuous-integration.yml)
![PHP](https://img.shields.io/packagist/php-v/jungi/common)

A minimal library that defines primitive building blocks of PHP code. It defines basic types and useful functions.

**Primitive types:**

* [`Equatable<T>`](https://piku235.gitbook.io/jungi-common/equatable)
* [`Result<T>`](https://piku235.gitbook.io/jungi-common/result)

## Installation

```text
composer require jungi/common
```

## Documentation

[GitBook](https://piku235.gitbook.io/jungi-common)

## Quick insight

### Equatable

```php
/** @implements Equatable<self> */
class Phone implements Equatable
{
    public function __construct(private string $value) {}
    
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

assert(true === (new Phone('(321) 456-1234'))->equals(new Phone('(321) 456-1234')));
assert(false === (new Phone('(321) 456-1234'))->equals(new Phone('(454) 456-1234')));
```

### Result

```php
final class Student
{
    public function __construct(
        public readonly StudentId $id,
        private(set) bool $active,
        private(set) string $name,
    ) {}
}

enum ClassEnrollmentError: string {
    case InactiveStudent = 'inactive_student';
    case StudentAlreadyEnrolled = 'student_already_enrolled';
    case NoSeatsAvailable = 'no_seats_available';
}

final class Class_
{
    public function __construct(
        public readonly ClassId $id,
        private(set) int $numberOfSeats,
        /** @var StudentId[] */
        private(set) array $students,
    ) {}
    
    /** @return Result<void, ClassEnrollmentError> */
    public function enroll(Student $student): Result
    {
        if (!$student->active) {
            return Result::error(ClassEnrollmentError::InactiveStudent);
        }
        if (in_iterable($student->id(), $this->students)) {
            return Result::error(ClassEnrollmentError::StudentAlreadyEnrolled);
        }
        if (count($this->students) >= $this->numberOfSeats) {
            return Result::error(ClassEnrollmentError::NoSeatsAvailable);
        }
        
        $this->students[] = $student->id();
        
        return Result::ok();
    }
}

class ClassController
{
    // PUT /classes/{classId}/students/{studentId}
    public function enrollToClass(string $classId, string $studentId)
    {
        // ... fetch the class and the student
        $r = $class->enroll($student);
        
        if ($r->isOk()) {
            return $this->created();
        }
        
        return match ($r->error) {
            ClassEnrollmentError::StudentAlreadyEnrolled => $this->noContent(), // returns 204
            default => $this->conflict($error), // returns 409 with the error
        };
    }
}
```

### Functions

```php
use function Jungi\Common\equals;
use function Jungi\Common\in_iterable;
use function Jungi\Common\iterable_unique;
use function Jungi\Common\array_equals;

/** @implements Equatable<self> */
class ContactInformation implements Equatable
{
    public function __construct(
        private Phone $phone,
        private ?Phone $mobile = null
    ) {}

    public function equals(self $other): bool
    {
        return $this->phone->equals($other->phone) && equals($this->mobile, $other->mobile);
    }
}

// equals()

$a = new ContactInformation(new Phone('(321) 456-1234'), new Phone('(886) 456-6543'));
$b = new ContactInformation(new Phone('(321) 456-1234'), new Phone('(886) 456-6543'));
assert(true === equals($a, $b);

$a = new ContactInformation(new Phone('(321) 456-1234'));
$b = new ContactInformation(new Phone('(321) 456-1234'), new Phone('(886) 456-6543'));
assert(false === equals($a, $b);

// array_equals()

$a = [new Phone('(321) 456-1234'), new Phone('(465) 799-4566')];
$b = [new Phone('(321) 456-1234'), new Phone('(465) 799-4566')];
assert(true === array_equals($a, $b));

$a = [new Phone('(321) 456-1234'), new Phone('(465) 799-4566')];
$b = [new Phone('(321) 456-1234')];
assert(false === array_equals($a, $b));

// in_iterable()

$iterable = [new Phone('(656) 456-7765'), new Phone('(321) 456-1234')];
assert(true === in_iterable(new Phone('(321) 456-1234'), $iterable));
assert(false === in_iterable(new Phone('(232) 456-1234'), $iterable));

// iterable_unique()

$unique = iterable_unique([
    new Phone('(321) 456-1234'),
    new Phone('(465) 799-4566'),
    new Phone('(321) 456-1234'),
]);
$expected = [
    new Phone('(321) 456-1234'),
    new Phone('(465) 799-4566'),
];
assert(true === array_equals($expected, $unique));
```
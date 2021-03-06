# Jungi Common

[![CI](https://github.com/jungi-php/common/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/jungi-php/common/actions/workflows/continuous-integration.yml)
![PHP](https://img.shields.io/packagist/php-v/jungi/common)

A minimal library that defines primitive building blocks of PHP code. It combines the advantages of functional
and object-oriented programming. All of this makes code easier to understand and less prone to errors.

**Primitive types:**

* [`Result<T, E>`](https://piku235.gitbook.io/jungi-common/result)
* [`Option<T>`](https://piku235.gitbook.io/jungi-common/option)
* [`Equatable<T>`](https://piku235.gitbook.io/jungi-common/equatable)

## Installation

```text
composer require jungi/common
```

## Documentation

[GitBook](https://piku235.gitbook.io/jungi-common)

## Quick insight

### Result

```php
interface Student
{
    public function id(): StudentId;
    public function isActive(): bool;
    public function name(): FullName;
}

enum ClassEnrollmentError: string {
    case InactiveStudent = 'inactive_student';
    case StudentAlreadyEnrolled = 'student_already_enrolled';
    case NoSeatsAvailable = 'no_seats_available';
}

class Class_
{
    private ClassId $id;
    private bool $finished;
    private int $numberOfSeats;
    /** @var StudentId[] */
    private array $students;
    
    /** @return Result<null, ClassEnrollmentError> */
    public function enroll(Student $student): Result
    {
        if (!$student->isActive()) {
            return err(ClassEnrollmentError::InactiveStudent);
        }
        if (in_iterable($student->id(), $this->students)) {
            return err(ClassEnrollmentError::StudentAlreadyEnrolled);
        }
        if (count($this->students) >= $this->numberOfSeats) {
            return err(ClassEnrollmentError::NoSeatsAvailable);
        }
        
        $this->students[] = $student->id();
        
        return ok();
    }
}

class ClassController
{
    // PUT /classes/{classId}/students/{studentId}
    public function enrollToClass(string $classId, string $studentId)
    {
        // ... fetch the class and the student
        return $class->enroll($student)
            ->andThen(fn() => $this->created()) // returns 201 if the result is ok
            ->getOrElse(fn(ClassEnrollmentError $error) => match ($error) {
                ClassEnrollmentError::StudentAlreadyEnrolled => $this->noContent(), // returns 204
                default => $this->badRequest($error), // returns 400 with the error
            });
    }
}
```

### Option

```php
interface UserRepositoryInterface
{
    /** @return Option<User> */
    public function find(string $username): Option;
}

class UserController
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    // GET /users/{username}
    public function getUser(string $username)
    {
        return $this->userRepository->find($username)
            ->andThen(fn(User $user) => $this->userData($user)) // maps the user to its resource representation
            ->getOrElse(fn() => $this->notFound()); // returns 404 response in case of the "none" option
    }
}
```

### Equatable

```php
/** @implements Equatable<Phone> */
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

### Functions

```php
use function Jungi\Common\equals;
use function Jungi\Common\in_iterable;
use function Jungi\Common\iterable_unique;
use function Jungi\Common\array_equals;

/** @implements Equatable<ContactInformation> */
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
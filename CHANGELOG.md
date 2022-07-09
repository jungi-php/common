# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2022-07-09

### Added
- Function `array_equals()` that checks if both arrays have the same keys and their values are equal

## [1.1.1] - 2022-05-27

### Added
- Template type declaration at `Equatable::equals()`. PHPStorm 2022.1 now handles generics types in @method tags, and it's also helpful when using an analysis tool.
- Missing @param tag at `Option::some()`
- Missing @param tags in local functions
- Missing @template tags in static methods of `Result`
- Missing @template tags in static methods of `Option`

## [1.1.0] - 2021-10-30

### Added
- Function `iterable_search()` that returns the first key where the given value is equal.

[unreleased]: https://github.com/jungi-php/common/compare/v1.2.0...HEAD
[1.2.0]: https://github.com/jungi-php/common/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/jungi-php/common/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/jungi-php/common/compare/v1.0.0...v1.1.0

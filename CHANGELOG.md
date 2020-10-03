# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 3.1.0 - 2020-10-03

### Added

- [#37](https://github.com/BlackBonjour/stdlib/issues/37) Introduced ```MapInterface::getIterator```, affecting
```HashMap```, ```Map``` and ```Sequence``` are now deprecated and will be removed with the next major version

### Changed

- Nothing

### Deprecated

- All public methods in ```Sequence``` that come from ```StdObject``` (```Sequence::__toString```,
```Sequence::clone```, ```Sequence::equals```, ```Sequence::hashCode```) 

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.0 - 2020-07-03

### Added

- Introduced ```StdString::createFromArrayOfChar``` to create a ```StdString``` instance from ```Character[]```
- Constructor of ```StdString``` will throw an ```InvalidArgumentException``` in case an invalid encoding was fetched
from using ```mb_internal_encoding```
- [#36](https://github.com/BlackBonjour/stdlib/issues/36) Added all possible types from PHPs internal function
[gettype()](https://www.php.net/manual/function.gettype.php) as new constants for ```Assert::typeOf``` and
```Assert::validate``` to use

### Changed

- [#29](https://github.com/BlackBonjour/stdlib/issues/29) Bumped PHPUnit to version 9.*
- Constructor of ```StdString``` no longer supports other types than strings
- ```HashMap``` can now throw ```JsonException``` as it uses ```json_encode``` to stringify injected keys of type array

### Deprecated

- Nothing

### Removed

- [#28](https://github.com/BlackBonjour/stdlib/issues/28) Dropped support for PHP 7.3 and lower
- [#30](https://github.com/BlackBonjour/stdlib/issues/30) ```Util\Arrays``` was removed
- Remove the useless feature to create a ```StdString``` instance from another ```StdString``` instance (if still
needed, just clone that instance instead!)

### Fixed

- ```CachedGenerator``` would throw errors in case the injected ```Generator``` yielded keys that weren't of type string
or integer

## 2.2.0 - 2019-05-08

### Added

- slice() and sort() to MapInterface and all classes implementing that interface

### Changed

- Assert::typeOf and Assert::validate supporting value type object

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- HashMap losing string identifier after sort

## 2.1.0 - 2019-03-25

### Added

- ArrayUtils
- Sequence
- toArray() to MapInterface and all classes implementing that interface

### Changed

- Nothing

### Deprecated

- Arrays

### Removed

- Nothing

### Fixed

- Nothing

## 2.0.0 - 2018-11-22

### Added

- CachedGenerator

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- [#23](https://github.com/BlackBonjour/stdlib/issues/23) Drop support for PHP 7.1

### Fixed

- Nothing

## 1.0.1 - 2018-10-12

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- [#24](https://github.com/BlackBonjour/stdlib/issues/24) Fixed HashMap::current returning false for NULL values

## 1.0.0 - 2018-08-01

### Added

- [#20](https://github.com/BlackBonjour/stdlib/issues/20) Added support for negative StdString offset
- [#21](https://github.com/BlackBonjour/stdlib/issues/21) Added Assert::notEmpty

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- [#22](https://github.com/BlackBonjour/stdlib/issues/22) Made HashMap::offsetSet void

## 0.5.1 - 2018-06-10

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Fix exception messages for Util\Arrays

## 0.5.0 - 2018-06-04

### Added

- [#15](https://github.com/BlackBonjour/stdlib/issues/15) Added library simple documentation
- [#16](https://github.com/BlackBonjour/stdlib/issues/16) Added Util\Arrays
- [#17](https://github.com/BlackBonjour/stdlib/issues/17) Added HashMap::sort
- Added Assert::empty (works like isset with multiple values)

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.4.1 - 2018-05-29

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- [#18](https://github.com/BlackBonjour/stdlib/issues/18) Fixed TypeError in HashMap::key when key was not string or null 

## 0.4.0 - 2018-04-28

### Added

- [#11](https://github.com/BlackBonjour/stdlib/issues/11) Added Util\HashMap and Util\Map (implementing Util\MapInterface)
- Added library specific exceptions
- Added Util\Number

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- [#12](https://github.com/BlackBonjour/stdlib/issues/12) Dropped support for PHP version 7.0

### Fixed

- Nothing

## 0.3.2 - 2018-04-12

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.3.1 - 2018-03-06

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- [#10](https://github.com/BlackBonjour/stdlib/issues/10) Fixed Util\Assert::validate throwing exceptions

## 0.3.0 - 2018-02-10

### Added

- Added Util\Assert::validate (same as Util\Assert::typeOf, but without throwing exceptions)

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.2.2 - 2018-02-07

### Added

- Nothing

### Changed

- Updated package information

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.2.1 - 2018-02-06

### Added

- Nothing

### Changed

- Made StdString and Character using Util\Assert

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.2.0 - 2018-02-06

### Added

- Added assertion utility

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 0.1.0 - 2018-01-30

### Added

- Fully implemented Object, String and Character class

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
[![Latest Stable Version](https://poser.pugx.org/blackbonjour/stdlib/v/stable)](https://packagist.org/packages/blackbonjour/stdlib)
[![Total Downloads](https://poser.pugx.org/blackbonjour/stdlib/downloads)](https://packagist.org/packages/blackbonjour/stdlib)
[![License](https://poser.pugx.org/blackbonjour/stdlib/license)](https://packagist.org/packages/blackbonjour/stdlib)

# stdlib
Standard library for PHP.

This library contains many helping classes for manipulating multibyte strings
and handling various types of arrays.

## Installation

It's recommend that you use [Composer](https://getcomposer.org) to install
Stdlib.

```bash
$ composer require blackbonjour/stdlib
```

This will install Stdlib and all required dependencies. Stdlib requires PHP 7.4
or newer.

## Usage

Following are a few example of what this library is capable of.

### Language

With ```StdString``` you have a powerful tool for manipulating strings.
Many functions are good known from other programming languages such as Java.

```php
$string      = new StdString('My vehicle has %d wheels.');
$translation = StdString::format($string, 4);

echo $translation; // My vehicle has 4 wheels.
```

```StdString``` is also multibyte compatible.

```php
$format      = '"%s" means "%s".';
$translation = StdString::format($format, 'Привет', new StdString('Hello'));

echo $translation; // "Привет" means "Hello".
```

```StdString``` instances can be created from string and ```Character[]```.

```StdString``` also implemented ```ArrayAccess```, which allows us to treat
```StdString``` instances like regular strings.

```php
$string = new StdString('FooBar');

echo $string[4]; // a
```

### Utils

Utils contains classes for easier handling with arrays and assertions.

#### Assert

With ```Assert::empty()``` you have the possibility to check multible values for
emptiness at once. This method works exactly the same as PHPs internal function
```empty()```.

```php
var_dump(Assert::empty(null, 0, '', [])); // true
var_dump(Assert::empty(null, 1, 'FooBar', [])); // false
```

For the opposite way use ```Assert::notEmpty``` to check if all
values are not empty.

```php
var_dump(Assert::notEmpty(123, 'FooBar', [123])); // true
var_dump(Assert::notEmpty(123, 'FooBar', [])); // false
```

With ```Assert::typeOf()``` to check if multiple values are of specified types
or are instances of specified classes. This method is supposed to react like PHP
itself. So if the values are not of specified types or classes, a TypeError
exception will be thrown. If that behaviour is not what you want, you can use
the method ```Assert::validate``` instead. That method will return ```true``` if
all values are of specified types or classes, false otherwise.

```php
$allowedTypes = [Assert::TYPE_INTEGER, Assert::TYPE_FLOAT];

var_dump(Assert::validate($types, 123, 456.7));
```

#### HashMap
With ```HashMap``` it is now possible to have arrays with objects as keys.

```php
$hashMap = new HashMap;
$hashMap->put(new StdString('FooBar'), ['foo' => 'bar']);

var_dump($hashMap->key() instanceof StdString); // Returns `true`

$hashMap->put(new stdClass, new StdString('NotFooBar'));

foreach ($hashMap as $key => $value) {
    echo StdString::format('%s => %s' . PHP_EOL, gettype($key), gettype($value));
}

/*
 * Output:
 *
 * object => array
 * object => object
 */
```

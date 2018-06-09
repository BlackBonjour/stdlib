# stdlib
Standard library for PHP.

## Installation

It's recommend that you use [Composer](https://getcomposer.org) to install Stdlib.

```bash
$ composer require blackbonjour/stdlib
```

This will install Stdlib and all required dependencies. Stdlib requires PHP 7.1 or newer.

## Usage

Following are a few example of what this library is capable of.

### Language

With ```StdString``` you have a powerful tool for manipulating strings.
Many functions are good known from other programming languages such as Java.

```php
$string = new StdString('My vehicle has %d wheels.');

if ($string->isEmpty() === false) {
    $translation = StdString::format($string, 4);
}

echo $translation; // Returns `My vehicle has 4 wheels.`
```

```StdString``` is also multibyte compatible.

```php
$format      = '"%s" means "%s".';
$translation = StdString::format($format, 'Привет', new StdString('Hello'));

echo $translation; // Returns `"Привет" means "Hello".`
```

```StdString``` methods always accept simple strings or StdString as arguments. The constructor also accepts an array of ```Character```.

```StdString``` also implemented ```ArrayAccess```, which allows us to treat ```StdString``` instances like regular strings.

```php
$string = new StdString('FooBar');
echo $string[4]; // Returns `a`
```

### Utils

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

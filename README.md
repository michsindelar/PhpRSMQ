![RSMQ: Redis Simple Message Queue for PHP](https://img.webmart.de/rsmq_wide.png)

# PHP Redis Simple Message Queue

PHP implementation of https://github.com/smrchy/rsmq.

## System Requirements

- php7
- php-redis extension

## Installation

The recommended way to install this library is through [Composer](http://getcomposer.org):

```bash
$ composer require michsindelar/PhpRSMQ "^1.0.0"
```

## Basic usage

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use PhpRSMQ\RedisSMQFacade;

$rsmq = new RedisSMQFacade('127.0.0.1', 6379);
$rsmq->sendMessage('myQueue', 'Hello world!');
```

## Tests

To execute the test suite, you'll need [Composer](http://getcomposer.org):

```bash
$ composer test
```

## TODO

- implement message receiving

## Contributing

Feel free to make a PR after consultation. Please follow these rules and standards when you write your code:
- SOLID
- KISS (Keep it simple, Stupid!)
- DRY (Don't Repeat Yourself)
- design patterns
- write documentation
- fluent interface
- write unit tests
- Law of Demeter

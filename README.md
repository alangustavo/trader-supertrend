# trader-supertrend Library 
This is a library that implements the supertrend indicator.

## Requirements

* PHP >= 7.0.0
* Extension php_trader

## Installation

This library is intended to be installed with composer.

~~~
composer require alangustavo/trader-supertrend
~~~

## Usage
```php
trader_supertrend(array $high, array $low, array $close, $timePeriod, $multiplier);
```
it returns an array with:
```php
[ 
    [
        'close' => 13.58332345,
        'type'  => '-1'
    ],
    [
        'close' => $12.5468844,
        'type'  => 1
    ]
]
```
where -1 = sell and 1 = buy.

# bnm-rates
PHP library used to get official exchange rates of National bank of Moldova.

[![Build Status](https://app.travis-ci.com/OsoianMarcel/bnm-rates.svg?branch=master)](https://app.travis-ci.com/OsoianMarcel/bnm-rates)
[![Latest Stable Version](https://poser.pugx.org/osoian/bnm-rates/v/stable)](https://packagist.org/packages/osoian/bnm-rates)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.5.9-8892BF.svg)](https://php.net/)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://github.com/OsoianMarcel/bnm-rates/blob/master/LICENSE)

## Installation

Use [Composer] to install the package:

```
$ composer require osoian/bnm-rates
```

## Examples

Here an example of how to use the library:

```php
<?php

use Osoian\BnmRates\BnmModel;
use Osoian\BnmRates\BnmRates;

$instance = new BnmRates();

/*
 * Get exchange rate
 */
$rate = $instance->getOne('EUR');
if ($rate) {
	var_dump([
		'value' => $rate->getValue(), // float(20.9869)
		'code' => $rate->getCode(), // string(3) "EUR"
		'name' => $rate->getName() // string(4) "Euro"
	]);
}

/*
 * What if we need only the value?
 */
$rate = $instance->getOne('EUR', true);
if ($rate) {
	var_dump($rate); // float(20.9869)
}

/*
 * What is we need multiple exchange rates?
 */
$rates = $instance->getMultiple(['EUR', 'USD', 'RON']);
if (!empty($rates)) {
	foreach ($rates as $key => $rate) {
		var_dump([
			'key' => $key, // 0, 1, 2
			'value' => $rate->getValue(), // float(20.9869), float(19.2567), float(4.6349)
			'code' => $rate->getCode(), // string(3) "EUR", string(3) "USD", string(3) "RON"
			'name' => $rate->getName() // string(4) "Euro", string(9) "US Dollar", string(12) "Romanian Leu"
		]);
	}
}

/*
 * What if we want to use assoc array?
 */
$instance->setAssocResult(true);
$rates = $instance->getMultiple(['EUR', 'USD', 'RON']);
if (!empty($rates)) {
	foreach ($rates as $key => $rate) {
		var_dump($key); // string(3) "EUR", string(3) "USD", string(3) "RON"
	}
}

/*
 * What if we need to use specific array key?
 */
$instance->setAssocClosure(function ($rate) {
	/**
	 * @var BnmModel $rate
	 */
	return $rate->getNumber() . '-' . $rate->getCode();
});
$rates = $instance->getMultiple(['EUR', 'USD', 'RON']);
if (!empty($rates)) {
	foreach ($rates as $key => $rate) {
		var_dump($key); // string(7) 978-EUR, string(7) "840-USD", string(7) "946-RON"
	}
}

/*
 * What if we call getters multiple times?
 */
$instance->setDate(new \DateTime('yesterday'));
// First call (no cache), request bnm.md web server
$st = microtime(true);
$instance->getOne('EUR');
$elapsed = microtime(true) - $st;
echo $elapsed . ' sec' . PHP_EOL; // 0.065252065658569 sec
// Second call (with cache), use cached results
$st = microtime(true);
$instance->getOne('USD');
$instance->getMultiple(['EUR', 'USD', 'RON']);
$elapsed = microtime(true) - $st;
echo $elapsed . ' sec' . PHP_EOL;// 2.6941299438477E-5 sec
// Yes, results are cached!

/*
 * What if we need results in other languages?
 */
$instance->setLocale('ro'); // Romanian language
$rate = $instance->getOne('RON');
if ($rate) {
	var_dump($rate->getName()); // string(12) "Leu romanesc"
}
$instance->setLocale('ru'); // Russian language
$rate = $instance->getOne('RON');
if ($rate) {
	var_dump($rate->getName()); // string(25) "Румынский Лей"
}

/*
 * What if we need to get all exchange rates?
 */
$rates = $instance->getAll();
if (!empty($rates)) {
	var_dump(count($rates)); // int(42)
	foreach ($rates as $key => $rate) {
		var_dump([
			'key' => $key, // string(7) "978-EUR", string(7) "840-USD", string(7) "643-RUB"
			'toString' => (string)$rate // string(9) "20.99 EUR", string(9) "19.26 USD", string(8) "0.34 RUB"
		]);
	}
}
```

## For Symfony users

Define the service and set default language (ro):

```yaml
osoian.bnm_rates:
        class: Osoian\BnmRates\BnmRates
        calls:
            - [ setLocale, ['ro'] ] # Optional config
```

Now you can call it:

```php
$this->get('osoian.bnm_rates')
	->setDate(new \DateTime('yesterday'))
	->getOne('EUR', true); // float(20.9869)
```


## Testing

``` bash
$ composer test
```

## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker].
* You can grab the source code at the package's [Git repository].

## License

All contents of this package are licensed under the [MIT license].

[Composer]: https://getcomposer.org
[issue tracker]: https://github.com/OsoianMarcel/bnm-rates/issues
[Git repository]: https://github.com/OsoianMarcel/bnm-rates
[MIT license]: LICENSE

# bnm-rates
PHP library used to get official exchange rates of National bank of Moldova

[![Build Status](https://travis-ci.org/OsoianMarcel/bnm-rates.svg?branch=master)](https://travis-ci.org/OsoianMarcel/bnm-rates)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.5.9-8892BF.svg)](https://php.net/)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)]()

## Examples

Here an example of how to use the library:

```php
<?php

use Osoian\BnmRates\BnmRates;

$bnmRates = new BnmRates();

$eurRate = $bnmRates->getOne('EUR');
if ($eurRate) {
	$value = $eurRate->getValue(); // 20.9869
}
```

## Testing

``` bash
$ composer test
```

## License

All contents of this package are licensed under the [MIT license].

[Composer]: https://getcomposer.org
[issue tracker]: https://github.com/OsoianMarcel/bnm-rates
[Git repository]: https://github.com/OsoianMarcel/bnm-rates
[MIT license]: LICENSE
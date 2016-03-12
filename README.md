PriceParser
===========

PHP 5.3+ library to parse price or money strings and get their currency and amount!

```php
<?php
use PriceParser\Price;

$priceWithCurrency = '12,499,50 €';
$price = new Price($priceWithCurrency);

//float value e.g. 15.20
$price->getAmount();

//currency symbol, e.g. $ or €
$price->getCurrencySymbol();

//currency iso code, e.g. EUR or USD
$price->getCurrencyIsoCode();

//currency name, e.g. Euro or US Dollar
$price->getCurrencyName();

//the raw value, e.g. 12,499,50 €
$price->getValue();

//price is valid if a currency can be found and amount is not empty or null
$price->isValid();
```



Installation
------------

Install the library using [composer](http://getcomposer.org/). Add the following to your `composer.json`:

```json
{
    "require": {
        "markus-g/price-parser": "0.1.*"
    }
}
```

Now run the `install` command.

```sh
$ composer.phar install
```

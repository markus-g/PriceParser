<?php
/**
 * User: Markus G.
 * Date: 03.02.2015
 */
namespace PriceParser;

/**
 * Class Price
 * @package PriceParser
 */
class Price
{
    /**
     * @var
     */
    private static $currencies;
    /**
     * @var string
     */
    private $value;
    /**
     * @var
     */
    private $currencySymbol;
    /**
     * @var
     */
    private $currencyIsoCode;
    /**
     * @var
     */
    private $currencyName;
    /**
     * @var
     */
    private $amount;

    /**
     * @param $priceString
     */
    public function __construct($priceString)
    {
        $priceString = trim($priceString);
        $this->value = $priceString;

        $priceString = self::encodeToUtf8($priceString);

        $this->parseCurrency($priceString);

        $this->parseAmount($priceString);

        return $this;
    }

    /**
     * @param $string
     * @return string
     */
    private static function encodeToUtf8($string)
    {
        $currentEncoding = mb_detect_encoding($string);
        if ($currentEncoding != 'UTF-8') {
            return mb_convert_encoding($string, "UTF-8", $currentEncoding);
        }
        return $string;
    }

    /**
     * @param $priceString
     */
    private function parseCurrency($priceString)
    {
        preg_match('/^(\D*)\s*([\d,\.]+)\s*(\D*)$/u', $priceString, $currencyMatches);
        $parsedCurrency = trim(!empty($currencyMatches[1]) ? $currencyMatches[1] : $currencyMatches[3]);

        if (!isset(self::$currencies)) {
            self::$currencies = json_decode(file_get_contents(__DIR__ . '/currencies.json'), true);
        }

        if (isset(self::$currencies[$parsedCurrency])) {
            $this->currencySymbol = self::$currencies[$parsedCurrency]['symbol']['default']['display'];
            $this->currencyIsoCode = $parsedCurrency;
            $this->currencyName = self::$currencies[$parsedCurrency]['name'];
        } else {
            foreach (self::$currencies as $currencyIsoCode => $currency) {
                if ($currency['symbol']['default']['display'] == $parsedCurrency || $currency['symbol']['native']['display'] == $parsedCurrency) {
                    $this->currencyIsoCode = $currencyIsoCode;
                    $this->currencySymbol = $parsedCurrency;
                    $this->currencyName = $currency['name'];
                    break;
                }
            }
        }
    }

    /**
     * @param $priceString
     */
    private function parseAmount($priceString)
    {
        $priceAmount = floatval(filter_var($priceString, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $this->amount = $priceAmount;
    }

    /**
     * @return mixed
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * @param mixed $currencyName
     */
    public function setCurrencyName($currencyName)
    {
        $this->currencyName = $currencyName;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @param mixed $currencySymbol
     */
    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;
    }

    /**
     * @return mixed
     */
    public function getCurrencyIsoCode()
    {
        return $this->currencyIsoCode;
    }

    /**
     * @param mixed $currencyIsoCode
     */
    public function setCurrencyIsoCode($currencyIsoCode)
    {
        $this->currencyIsoCode = $currencyIsoCode;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}
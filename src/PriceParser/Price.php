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
        if (empty($currencyMatches)) {
            $this->currencySymbol = '';
            $this->currencyIsoCode = '';
            $this->currencyName = '';
            return;
        } else {
            if (!isset(self::$currencies)) {
                self::$currencies = json_decode(file_get_contents(__DIR__ . '/currencies.json'), true);
            }
            $currencyMatches = array_reverse($currencyMatches);
            foreach ($currencyMatches as $currencyMatch) {
                $currencyMatch = $this->mbTrim(mb_strtoupper($currencyMatch));
                if (!$currencyMatch) {
                    continue;
                }
                foreach (self::$currencies as $currency) {
                    if ($currency['iso']['code'] == $currencyMatch || $currency['symbol']['default']['display'] == $currencyMatch) {
                        $this->currencySymbol = $currency['symbol']['default']['display'];
                        $this->currencyIsoCode = $currency['iso']['code'];
                        $this->currencyName = $currency['name'];
                        break 2;
                    }
                }
            }
        }
    }

    /**
     * @param $str
     * @return mixed
     */
    public function mbTrim($str)
    {
        return preg_replace("/(^\s+)|(\s+$)/us", "", $str);
    }

    /**
     * @param $priceString
     */
    private function parseAmount($priceString)
    {
        $priceString = str_replace(',', '.', $priceString);
        $priceAmount = floatval(filter_var($priceString, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $this->amount = $priceAmount;
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
     * Price is valid if a currency can be found and amount is not empty or null
     * @return bool
     */
    public function isValid()
    {
        if ($this->currencyIsoCode == '') {
            return false;
        }
        if ($this->amount == '' || $this->amount == null) {
            return false;
        }
        return true;
    }
}
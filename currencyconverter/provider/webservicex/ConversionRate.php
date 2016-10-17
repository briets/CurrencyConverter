<?php namespace Rickbrunken\CurrencyConverter\Provider\Webservicex;

/**
 * Description of ConversionRate
 *
 * @author rick.brunken
 */
class ConversionRate
{
    public $FromCurrency;
    public $ToCurrency;

    public function __construct($fromCurrency, $toCurrency)
    {
        $this->FromCurrency = $fromCurrency;
        $this->ToCurrency = $toCurrency;
    }
}
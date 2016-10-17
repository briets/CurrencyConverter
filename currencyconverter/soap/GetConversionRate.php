<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of GetConversionRate
 *
 * @author rick.brunken
 * 
 * @pw_set nillable=false
 * @pw_element string $FromCurrency The currency to convert from
 * @pw_set nillable=false
 * @pw_element string $ToCurrency The currency to convert to
 * @pw_complex GetConversionRate The complex type name definition
 */
class GetConversionRate
{
    public $FromCurrency;
    public $ToCurrency;

    function __construct($FromCurrency, $ToCurrency)
    {
        $this->FromCurrency = $FromCurrency;
        $this->ToCurrency = $ToCurrency;
    }
}
<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of GetConversionRate
 *
 * @author rick.brunken
 * 
 * @pw_set nillable=false
 * @pw_element string $Currency The currency to convert to
 * @pw_complex GetCurrencyRate The complex type name definition
 */
class GetCurrencyRate
{
    public $Currency;

    function __construct($Currency)
    {
        $this->Currency = $Currency;
    }
}
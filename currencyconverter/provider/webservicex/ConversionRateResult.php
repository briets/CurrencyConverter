<?php namespace Rickbrunken\CurrencyConverter\Provider\Webservicex;

/**
 * Description of ConversionRateResult
 *
 * @author rick.brunken
 */
class ConversionRateResult
{
    /** @var double */
    public $ConversionRateResult;

    function __construct($ConversionRateResult)
    {
        $this->ConversionRateResult = $ConversionRateResult->ConversionRateResult;
    }


}
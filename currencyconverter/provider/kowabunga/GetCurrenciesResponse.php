<?php namespace Rickbrunken\CurrencyConverter\Provider\Kowabunga;

/**
 * Description of GetCurrenciesResponse
 *
 * @author rick.brunken
 */
class GetCurrenciesResponse
{
    /** @var array */
    public $GetCurrenciesResult;

    /**
     * Constructor
     * @param GetCurrenciesResponse $GetCurrenciesResponse
     */
    function __construct($GetCurrenciesResponse)
    {
        $this->GetCurrenciesResult = $GetCurrenciesResponse->GetCurrenciesResult->string;
    }
}
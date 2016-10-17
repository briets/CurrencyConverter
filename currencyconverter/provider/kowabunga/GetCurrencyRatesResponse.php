<?php namespace Rickbrunken\CurrencyConverter\Provider\Kowabunga;

/**
 * Description of GetCurrencyRatesResponse
 *
 * @author rick.brunken
 */
class GetCurrencyRatesResponse
{
    /** @var double */
    public $GetCurrencyRatesResult = array();

    /**
     * Constructor
     * @param GetCurrencyRatesResponse $GetCurrencyRatesResponse
     */
    public function __construct($GetCurrencyRatesResponse)
    {
        $xml = \simplexml_load_string($GetCurrencyRatesResponse->GetCurrencyRatesResult->any);
        foreach ($xml->NewDataSet->Table as $currency) {
            $this->GetCurrencyRatesResult[$currency->Description->__toString()] = (double) $currency->Rate;
        }
    }
}
<?php namespace Rickbrunken\CurrencyConverter\Provider\Kowabunga;

/**
 * Description of GetCurrencyRates
 *
 * @author rick.brunken
 */
class GetCurrencyRates
{
    /** @var string */
    public $RateDate;

    /**
     * Constructor
     * @param string $rateDate
     */
    public function __construct($rateDate = null)
    {
        $this->RateDate = date('Y-m-d\T00:00:00', $rateDate === null ? (new \DateTime())->getTimestamp()
                - 3600 // TODO: Seems something strange with the time, probably timezone: fix this
                : $rateDate->getTimestamp());
    }
}
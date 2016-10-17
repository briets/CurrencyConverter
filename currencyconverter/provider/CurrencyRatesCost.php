<?php namespace Rickbrunken\CurrencyConverter\Provider;

/**
 * Description of CurrencyRatesCost
 *
 * @author rick.brunken
 */
class CurrencyRatesCost
{
    /**
     * All supported currencies
     * @var array
     */
    public $currencies;

    /**
     * Cost of currencyrates request
     * @var float
     */
    public $cost;

    /**
     *
     * @var Provider
     */
    public $provider;

    public function hasCurrencies(array $currencies)
    {
        return count(array_intersect($currencies, $this->currencies)) === count($currencies);
    }
}
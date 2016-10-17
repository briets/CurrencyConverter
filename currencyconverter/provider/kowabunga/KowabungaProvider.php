<?php namespace Rickbrunken\CurrencyConverter\Provider\Kowabunga;

use Rickbrunken\CurrencyConverter\Cache\Cache;
use Rickbrunken\CurrencyConverter\Provider\CurrencyRatesCost;
use Rickbrunken\CurrencyConverter\Provider\WsdlProvider;

/**
 * Description of KowabungaProvider
 *
 * @author rick.brunken
 */
class KowabungaProvider extends WsdlProvider
{
    protected $wsdl = 'http://currencyconverter.kowabunga.net/converter.asmx?WSDL';
    protected $name = 'kowabunga';

    /**
     * Setup provider. The provider should be available after this method is
     * called
     */
    public function setup()
    {
        if (!$this->available) {
            $currencies = Cache::get('provider.kowabunga.currencies');
            if (!$currencies) {
                // Get all currencies from provider
                $GetCurrencies = new GetCurrencies();
                $GetCurrenciesResponse = new GetCurrenciesResponse($this->invokeMethod('GetCurrencies', $GetCurrencies));
                if (is_array($GetCurrenciesResponse->GetCurrenciesResult)) {
                    $this->currencies = $GetCurrenciesResponse->GetCurrenciesResult;
                }
                // Store available currencies in cache
                Cache::set('provider.kowabunga.currencies', $this->currencies, $this->configuration->get('cacheCurrenciesRetention', 86400));
            } else {
                $this->currencies = $currencies;
            }
            $this->available = true;
        }
    }

    /**
     * Try to get needed currencies
     * @param array $currencies Needed currencies
     * @return array Returns a associative array like ['EUR' => 1, 'USD' => 1.0701]
     * @throws ProviderUnavailableException
     */
    public function getCurrencyRates(array $currencies)
    {
        $this->setup();
        $GetCurrencyRates = new GetCurrencyRates();
        $GetCurrencyRatesResponse = new GetCurrencyRatesResponse($this->invokeMethod('GetCurrencyRates', $GetCurrencyRates));
        foreach ($GetCurrencyRatesResponse->GetCurrencyRatesResult as $currency => $rate) {
            if (!in_array($currency, $currencies)) {
                unset($GetCurrencyRatesResponse->GetCurrencyRatesResult[$currency]);
            }
        }
        if (in_array($this->mainCurrency, $currencies)) {
            // Add main currency to response
            $GetCurrencyRatesResponse->GetCurrencyRatesResult[$this->mainCurrency] = 1;
        }
        return $GetCurrencyRatesResponse->GetCurrencyRatesResult;
    }

    /**
     * Get the predicted cost to fetch currencies
     * @param array $currencies Needed currencies
     * @return CurrencyRatesCost
     */
    public function getCurrencyRatesCost(array $currencies)
    {
        $cost = new CurrencyRatesCost();
        $cost->currencies = $this->getSupportedCurrencies($currencies);
        $cost->cost = $this->configuration->get('currencyRatesCost', 0);
        $cost->provider = $this;
        return $cost;
    }
}
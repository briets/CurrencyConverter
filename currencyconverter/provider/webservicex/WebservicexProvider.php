<?php namespace Rickbrunken\CurrencyConverter\Provider\Webservicex;

use Rickbrunken\CurrencyConverter\Configuration;
use Rickbrunken\CurrencyConverter\Provider\CurrencyRatesCost;
use Rickbrunken\CurrencyConverter\Provider\Webservicex\ConversionRate;
use Rickbrunken\CurrencyConverter\Provider\Webservicex\ConversionRateResult;
use Rickbrunken\CurrencyConverter\Provider\WsdlProvider;

/**
 * Description of WebservicexService
 *
 * @author rick.brunken
 */
class WebservicexProvider extends WsdlProvider
{
    protected $wsdl = 'http://www.webservicex.net/currencyconvertor.asmx?WSDL';
    protected $name = 'webservicex';
    protected $currencies = [
        'AFA', 'ALL', 'DZD', 'ARS', 'AWG', 'AUD', 'BSD', 'BHD', 'BDT',
        'BBD', 'BZD', 'BMD', 'BTN', 'BOB', 'BWP', 'BRL', 'GBP', 'BND',
        'BIF', 'XOF', 'XAF', 'KHR', 'CAD', 'CVE', 'KYD', 'CLP', 'CNY',
        'COP', 'KMF', 'CRC', 'HRK', 'CUP', 'CYP', 'CZK', 'DKK', 'DJF',
        'DOP', 'XCD', 'EGP', 'SVC', 'EEK', 'ETB', 'EUR', 'FKP', 'GMD',
        'GHC', 'GIP', 'XAU', 'GTQ', 'GNF', 'GYD', 'HTG', 'HNL', 'HKD',
        'HUF', 'ISK', 'INR', 'IDR', 'IQD', 'ILS', 'JMD', 'JPY', 'JOD',
        'KZT', 'KES', 'KRW', 'KWD', 'LAK', 'LVL', 'LBP', 'LSL', 'LRD',
        'LYD', 'LTL', 'MOP', 'MKD', 'MGF', 'MWK', 'MYR', 'MVR', 'MTL',
        'MRO', 'MUR', 'MXN', 'MDL', 'MNT', 'MAD', 'MZM', 'MMK', 'NAD',
        'NPR', 'ANG', 'NZD', 'NIO', 'NGN', 'KPW', 'NOK', 'OMR', 'XPF',
        'PKR', 'XPD', 'PAB', 'PGK', 'PYG', 'PEN', 'PHP', 'XPT', 'PLN',
        'QAR', 'ROL', 'RUB', 'WST', 'STD', 'SAR', 'SCR', 'SLL', 'XAG',
        'SGD', 'SKK', 'SIT', 'SBD', 'SOS', 'ZAR', 'LKR', 'SHP', 'SDD',
        'SRG', 'SZL', 'SEK', 'CHF', 'SYP', 'TWD', 'TZS', 'THB', 'TOP',
        'TTD', 'TND', 'TRL', 'USD', 'AED', 'UGX', 'UAH', 'UYU', 'VUV',
        'VEB', 'VND', 'YER', 'YUM', 'ZMK', 'ZWD', 'TRY'
    ];

    /**
     * Constructor
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        $this->setup();
    }

    /**
     * Setup provider. The provider should be available after this method is
     * called
     */
    public function setup()
    {
        $this->available = true;
    }

    /**
     * Get conversation rate from provider
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return double
     */
    protected function getConversionRate($fromCurrency, $toCurrency)
    {
        $response = new ConversionRateResult(
            $this->executeSoapMethod("ConversionRate", new ConversionRate($fromCurrency, $toCurrency)));
        //if ($response->ConversionRateResult <= 0) {
        //    $this->setUnavailable();
        //}
        return $response->ConversionRateResult;
    }

    /**
     * Get currency rates from provider
     * @param array $currencies Currencies to get
     * @return array
     */
    public function getCurrencyRates(array $currencies)
    {
        $result = array();
        foreach ($currencies AS $currency) {
            $ConversionRateResult = new ConversionRateResult(
                $this->invokeMethod('ConversionRate', new ConversionRate($this->mainCurrency, $currency)));
            if ($ConversionRateResult->ConversionRateResult > 0) {
                $result[$currency] = $ConversionRateResult->ConversionRateResult;
            } else {
                $this->setUnavailable('response from provider is incorrect');
            }
        }
        return $result;
    }

    /**
     * Get currency rates cost
     * @param array $currencies Currencies to get cost for
     * @return CurrencyRatesCost
     */
    public function getCurrencyRatesCost(array $currencies)
    {
        $cost = new CurrencyRatesCost();
        $cost->currencies = $this->getSupportedCurrencies($currencies);
        $cost->cost = count($currencies) * $this->configuration->get('conversionRateCost', 0);
        $cost->provider = $this;
        return $cost;
    }
}
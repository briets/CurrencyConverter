<?php namespace Rickbrunken\CurrencyConverter\Provider;

use Rickbrunken\CurrencyConverter\Configuration;
use Rickbrunken\CurrencyConverter\Exception\ProviderUnavailableException;

/**
 * Description of Provider
 *
 * @author rick.brunken
 */
abstract class Provider
{
    /**
     * Name of the provider
     * @var string 
     */
    protected $name = '';

    /**
     * Configuration
     * @var Configuration
     */
    protected $configuration;

    /**
     * All supported currencies
     * @var array 
     */
    protected $currencies = [];

    /**
     * Provider activity state
     * @var boolean
     */
    protected $available = false;

    /**
     * Main currency to which all rates are relative
     * @var string
     */
    protected $mainCurrency = 'EUR';

    /**
     * Constructor
     * @param \Rickbrunken\CurrencyConverter\Provider\Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Setup provider. The provider should be available after this method is
     * called. This method should be called from extending class.
     */
    public abstract function setup();

    /**
     * Validate if provider is available
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available === true;
    }

    /**
     * Set provider to unavailable. When a provider is unavailable no data can
     * be fetched.
     * @param string $reason Why is the provider down
     */
    protected function setUnavailable($reason)
    {
        $this->available = false;
        $this->currencies = [];
        throw new ProviderUnavailableException(get_class($this));
    }

    /**
     * Get the name of this provider
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Get main currency to which all rates are relative
     * @return string
     */
    public function getMainCurrency()
    {
        return $this->mainCurrency;
    }

    /**
     * Get all currencies that are available
     * @param array $currencies Needed currencies
     */
    public function getSupportedCurrencies(array $currencies)
    {
        $this->setup();
        return array_intersect($currencies, $this->currencies);
    }

    /**
     * Get the predicted cost to fetch currencies
     * @param array $currencies Needed currencies
     * @return CurrencyRatesCost
     */
    public abstract function getCurrencyRatesCost(array $currencies);

    /**
     * Try to get needed currencies
     * @param array $currencies Needed currencies
     * @return array Returns a associative array like ['EUR' => 1, 'USD' => 1.0701]
     * @throws ProviderUnavailableException
     */
    public abstract function getCurrencyRates(array $currencies);
}
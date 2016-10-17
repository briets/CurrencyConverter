<?php namespace Rickbrunken\CurrencyConverter;

use PhpWsdl;
use Rickbrunken\CurrencyConverter\Cache\Cache;
use Rickbrunken\CurrencyConverter\Exception\ProviderUnavailableException;
use Rickbrunken\CurrencyConverter\Exception\UnavailableCurrencyException;
use Rickbrunken\CurrencyConverter\Exception\UnsupportedCurrencyException;
use Rickbrunken\CurrencyConverter\Provider\Provider;
use Rickbrunken\CurrencyConverter\Soap\SoapService;
use Rickbrunken\CurrencyConverter\Storage\Storage;

/**
 * CurrencyConverterWebservice
 *
 * @author  Rick Brunken
 * @email   rick.brunken@gmail.com
 */
class CurrencyConverter
{
    /** @var Configuration */
    protected $configuration;

    /**
     * Array of all supported currencies
     * @var array
     */
    protected $currencies = array();
    protected $providers = array();
    protected $maxCostPerRequest;

    /**
     * Constructor
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        // Set configuration options
        $this->maxCostPerRequest = $configuration->get('maxCostPerRequest');

        // Setup the storage
        // Configure caching and storage
        Cache::configure($configuration->getAsConfiguration('cache'));
        Storage::configure($configuration->getAsConfiguration('storage'));
        // Load all supported currencies
        $this->currencies = $this->configuration->get('currencies', array());

        // Load all configurated providers
        $this->loadProviders();
    }

    /**
     * Load all providers
     */
    protected function loadProviders()
    {
        // Iterate through all services in configuration
        foreach ($this->configuration->get('providers') as $key => $service) {
            $class = $service['class'];

            // Get the configuration for this service
            $configuration = $this->configuration->getAsConfiguration('providers.' . $key . '.options');

            // Instantiate the service and store in services array
            $this->providers[] = new $class($configuration);
        }
    }

    /**
     * Returns the loaded configuration
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get all currencies
     * @return array Returns an array of currencies
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Validate that a currency is supported
     * @param string $currency
     * @throws UnsupportedCurrencyException
     */
    public function supportsCurrency($currency)
    {
        if (!in_array(strtoupper($currency), $this->currencies)) {
            throw new UnsupportedCurrencyException('currency \'' . $currency . '\' not supported');
        }
    }

    /**
     * Validates if all currencies are supported
     * @param array $currencies Currencies to validate
     * @throws UnsupportedCurrencyException
     */
    public function supportsCurrencies(array $currencies)
    {
        foreach ($currencies as $currency) {
            $this->supportsCurrency($currency);
        }
    }

    /**
     * Get rates for currencies
     * @param array $currencies All currencies to get rates for
     * @param integer $maxCost Maximum cost for request
     * @return type
     * @throws UnsupportedCurrencyException
     * @throws UnavailableCurrencyException
     */
    public function getCurrencyRates(array $currencies)
    {
        $this->supportsCurrencies($currencies);

        $found = array();
        $left = $currencies;
        // Fetch all currency rates from cache
        foreach ($currencies as $currency) {
            $rate = Cache::get($currency);
            if ($rate !== false) {
                $found[$currency] = (float) $rate;
                $left = \array_diff($left, [$currency]);
            }
        }

        // If there is anything left fetch it from the providers
        if (count($left) > 0) {
            /* @var $bestProvider CurrencyRatesCost */
            $bestProvider = null;
            // Check per provider the predicted cost
            foreach ($this->providers as /* @var $provider Provider */ $provider) {
                $cost = $provider->getCurrencyRatesCost($left);
                if ($cost->hasCurrencies($left) && // Does the provider have all needed languages?
                    ($this->maxCostPerRequest === null || $cost->cost <= $this->maxCostPerRequest) // Is this provider cheaper than maxCost?
                    && ($bestProvider === null || $cost->cost < $bestProvider->cost)) { // Is this provider cheaper?
                    $bestProvider = $cost;
                }
            }
            if ($bestProvider !== null) {
                // Try to get the needed currency rates from provider
                try {
                    $provider = $bestProvider->provider;
                    Storage::storeCollectLog($provider->getName(), $bestProvider->cost); // Log to Storage
                    foreach ($provider->getCurrencyRates($left) as $currency => $rate) {
                        $found[$currency] = (float) $rate;
                        Cache::set($currency, $rate);
                        Storage::storeCurrency($currency, $rate);
                        $left = array_diff($left, [$currency]);
                    }
                } catch (ProviderUnavailableException $ex) {
                    // The provider is unavailable at the moment, try the others
                    foreach ($this->getCurrencyRates($left) AS $currency => $rate) {
                        $found[$currency] = (float) $rate;
                        $left = array_diff($left, [$currency]);
                    }
                }
            }
        }

        // If there is anything left throw UnavailableCurrencyException
        if (count($left) > 0) {
            throw new UnavailableCurrencyException($left[0]);
        }
        return $found;
    }

    /**
     *
     * @param type $fromCurrency
     * @param type $toCurrency
     * @return type
     * @throws UnsupportedCurrencyException
     * @throws UnavailableCurrencyException
     */
    public function getConversionRate($fromCurrency, $toCurrency)
    {
        $rates = $this->getCurrencyRates([$fromCurrency, $toCurrency]);
        return (float) $rates[$toCurrency] / $rates[$fromCurrency];
    }

    public function getGraphData($currency, $fromDatetime, $toDatetime)
    {
        return Storage::getGraphData($currency, $fromDatetime, $toDatetime);
    }

    /**
     * Run cron tasks needed for graph data
     */
    protected function cron()
    {
        // Get all currency rates
        echo "Running cron...";
        $this->getCurrencyRates($this->currencies);
    }

    /**
     * Handle CLI commands
     * @param array $argv All arguments passed to the cli
     * @return type
     */
    public function handleCliCommand(array $argv)
    {
        if (\php_sapi_name() !== 'cli') {
            exit('Can only be used from cli');
        }
        if (\count($argv) < 2) {
            exit('No command given');
        }
        switch ($argv[1]) {
            case 'storage':
                switch ($argv[2]) {
                    case 'install': Storage::install();
                        echo "Storage installed";
                        return;
                    case 'uninstall': Storage::uninstall();
                        echo "Storage uninstalled";
                        return;
                    case 'flush': Storage::flush();
                        echo "Storage flushed";
                        return;
                    default: echo "Command 'storage " . $argv[2] . "' not found";
                        return;
                }
                return;
            case 'cron':
                $this->cron();
                return;
            case 'cache':
                switch ($argv[2]) {
                    case 'flush':
                        Cache::flush();
                        echo "Cache flushed";
                        return;
                    default: "Command 'cache " . $argv[2] . "' not found";
                }
                return;
            case 'adduser':
                if (!$argv[2] || !$argv[3]) {
                    echo "adduser expects 2 parameters: <Username> <Password>";
                    return;
                }
                Storage::storeUser($argv[2], $argv[3]);
            case 'get':
                switch ($argv[2]) {
                    case 'ConversionRate':
                        if (!$argv[3] || !$argv[4]) {
                            echo "get ConversionRate expects 2 parameters: <FromCurrency> <ToCurrency>";
                            return;
                        }
                        echo $this->getConversionRate($argv[3], $argv[4]);
                        return;
                    case 'CurrencyRate':
                        if (!$argv[3]) {
                            echo "get CurrencyRate expects 1 parameter: <Currency>";
                            return;
                        }
                        echo $this->getCurrencyRates([$argv[3]])[$argv[3]];
                }
                return;
            case 'install': Storage::install();
                echo "Storage installed";
                return;
            default: echo "Command '" . $argv[1] . "' not found";
                return;
        }
    }

    /**
     * Handle HTTP commands
     */
    public function handleHttpCommand()
    {
        $phpwsdl = PhpWsdl::CreateInstance(
                null, // Namespace
                null, // SOAP endpoint URI
                null, // WSDL cache folder
                Array(// All files with WSDL definitions in comments
                __DIR__ . '/soap/SoapService.php',
                __DIR__ . '/soap/GetConversionRate.php',
                __DIR__ . '/soap/GetConversionRateResponse.php',
                __DIR__ . '/soap/GetCurrencyRate.php',
                __DIR__ . '/soap/GetCurrencyRateResponse.php',
                __DIR__ . '/soap/GetGraph.php',
                __DIR__ . '/soap/GraphData.php',
                __DIR__ . '/soap/GraphDataArray.php',
                __DIR__ . '/soap/GetGraphResponse.php',
                ), null, // Classname of service
                null, // Method definitions
                null, // Complex types
                false, // Don't send WSDL right now
                false);
        // Disable wsdl caching
        \ini_set('soap.wsdl_cache_enabled', 0);
        PhpWsdl::$CacheTime = 0;
        if ($phpwsdl->IsWsdlRequested()) {
            // Make the WSDL human readable
            $phpwsdl->Optimize = false;
        }

        // Load SOAP service handler
        $soapService = new SoapService($this);
        $secureClass = $this->configuration->get('soap.secure.class');
        if ($secureClass !== null) {
            // Wrap the SOAP service handler in a secure wrapper
            $soapService = new $secureClass($soapService);
        }
        // Start the SOAP server
        $phpwsdl->RunServer(null, $soapService);
    }
}
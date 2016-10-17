<?php
return [
    'maxCostPerRequest' => 4.04, // Maximum cost to handle a request when no data in cache and storage
    'currencies' => [ // Supported currencies
        'USD',
        'EUR',
        'GBP',
        'AUD',
        'HKD',
        'DKK',
        'RUB',
        'CAD',
    ],
    'cache' => [
        'class' => '\Rickbrunken\CurrencyConverter\Cache\MemcacheService',
        'host' => '127.0.0.1', // Memcache host to connect to
        'port' => 11211, // Memcache port to connect to,
        'namespace' => null, // Memcache namespace to use,
        'retention' => 10, // Default retention
    ],
    'storage' => [
        'class' => '\Rickbrunken\CurrencyConverter\Storage\RedbeanService',
        'connection' => 'sqlite:' . __DIR__ . '\storage\database.db'
    ],
    'soap' => [
        'secure' => [ // Auth provider
            'class' => '\Rickbrunken\CurrencyConverter\Soap\SecureSoapServiceWrapper',
        ],
    ],
    'providers' => [ // All services that will be loaded with their options
        [
            'class' => '\Rickbrunken\CurrencyConverter\Provider\Webservicex\WebservicexProvider',
            'options' => [
                'conversionRateCost' => 0.1, // Cost per collectConversionRate request
            ],
        ],
        [
            'class' => '\Rickbrunken\CurrencyConverter\Provider\Kowabunga\KowabungaProvider',
            'options' => [
                'currencyRatesCost' => 0.2, // Cost per collectCurrencyRates request,
                'cacheCurrenciesRetention' => 86400 // Retention of getCurrencies
            ],
        ],
    ],
];

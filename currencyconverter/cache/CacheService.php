<?php namespace Rickbrunken\CurrencyConverter\Cache;

use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of Cacher
 *
 * @author rick.brunken
 */
abstract class CacheService
{
    /**
     *
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public abstract function get($key);

    public abstract function set($key, $value, $retention = null);

    public abstract function flush();
}
<?php namespace Rickbrunken\CurrencyConverter\Cache;

use Memcache;
use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of MemCache
 *
 * @author rick.brunken
 */
class MemcacheService extends CacheService
{
    /**
     *
     * @var Memcache 
     */
    protected $memcache;

    /**
     * Host to connect to
     * @var string
     */
    protected $host;

    /**
     * Port to connect to
     * @var integer
     */
    protected $port;

    /**
     * Namespace to prepend before all keys, this is used when this is not
     * the only system using the cache
     * @var string
     */
    protected $namespace;

    /**
     * Default retention
     * @var integer
     */
    protected $retention;

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        $this->host = $configuration->get('host', '127.0.0.1');
        $this->port = $configuration->get('port', 11211);
        $this->namespace = $configuration->get('namespace');
        $this->retention = $configuration->get('retention', 600);
    }

    /**
     * Get a Memcache connection
     * @return Memcache
     */
    protected function getConnection()
    {
        if ($this->memcache === null) {
            $this->memcache = new Memcache();
            $this->memcache->connect($this->host, $this->port);
        }
        return $this->memcache;
    }

    /**
     * Get the key prepended with the namespace if available
     * @param string $key
     * @return string
     */
    protected function getNamespacedKey($key)
    {
        return $this->namespace !== null ? $this->namespace . '.' . $key : $key;
    }

    public function get($key)
    {
        return $this->getConnection()->get($this->getNamespacedKey($key));
    }

    public function set($key, $value, $retention = null)
    {
        $this->getConnection()->set($this->getNamespacedKey($key), $value, 0, $retention
            !== null ? $retention : $this->retention);
    }

    public function flush()
    {
        $this->getConnection()->flush();
    }
}
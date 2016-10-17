<?php namespace Rickbrunken\CurrencyConverter\Cache;

use Exception;
use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of Cache
 *
 * @author rick.brunken
 */
abstract class Cache
{
    protected static $isConfigured = false;

    /**
     *
     * @var Catcher
     */
    protected static $catcher = null;

    public static function configure(Configuration $configuration)
    {
        $class = $configuration->get('class');
        static::$catcher = new $class($configuration);
        static::$isConfigured = true;
    }

    protected function isConfigured()
    {
        if (!static::$isConfigured) {
            throw new Exception('cache is not configured');
        }
    }

    public static function get($key)
    {
        static::isConfigured();
        return static::$catcher->get($key);
    }

    public static function set($key, $value, $retention = null)
    {
        static::isConfigured();
        return static::$catcher->set($key, $value, $retention);
    }

    public static function flush()
    {
        static::isConfigured();
        return static::$catcher->flush();
    }
}
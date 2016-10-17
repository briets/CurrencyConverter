<?php namespace Rickbrunken\CurrencyConverter\Storage;

use Exception;
use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of Storage2
 *
 * @author rick.brunken
 */
abstract class Storage
{
    protected static $isConfigured = false;

    /**
     *
     * @var StorageService
     */
    protected static $storer;

    public static function configure(Configuration $configuration)
    {
        $class = $configuration->get('class');
        static::$storer = new $class($configuration);
        static::$isConfigured = true;
    }

    protected function isConfigured()
    {
        if (!static::$isConfigured) {
            throw new Exception('cache is not configured');
        }
    }

    public static function __callStatic($method, $arguments)
    {
        static::isConfigured();
        if (!method_exists(static::$storer, $method)) {
            throw new \Exception("StorageService does not have method " . $method);
        }
        return call_user_method_array($method, static::$storer, $arguments);
    }

    /**
     * Install storage
     */
    public static function install()
    {
        static::isConfigured();
        static::$storer->install();
    }

    /**
     * Uninstall storage
     */
    public static function uninstall()
    {
        static::isConfigured();
        static::$storer->uninstall();
    }

    /**
     * Flush storage
     */
    public static function flush()
    {
        static::isConfigured();
        static::$storer->flush();
    }

  

}
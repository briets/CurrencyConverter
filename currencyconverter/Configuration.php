<?php namespace Rickbrunken\CurrencyConverter;

/**
 * Configuration can be used to get values from a multidimensional array
 * using a path. A path consist of array keys joined by dots (.) An example
 * of a path is: servicemanager.services.0.priority. This value is fetched
 * from $configuration['servicemanager']['services']['0']['priority'].
 *
 * @author rick.brunken
 */
class Configuration
{
    /** @var array */
    protected $configuration;

    public function __construct(array $configuration = array())
    {
        $this->configuration = $configuration;
    }

    /**
     * Get a value from the configuration by path
     * @param type $path The path to get the value of
     * @param type $default The value to return if the path is not found
     * @return type mixed
     */
    public function get($path, $default = null)
    {
        $keys = \explode('.', $path);
        $arr = &$this->configuration;
        foreach ($keys as $key) {
            if (!isset($arr[$key])) {
                return $default;
            }
            $arr = &$arr[$key];
        }
        return $arr;
    }

    /**
     * Get a value from the configuration as a new configuration. If the path
     * is not found or the value is not of type array an empty configuration
     * will be returned.
     * @param type $path The path to get the value of
     * @return \Rickbrunken\CurrencyConverter\Configuration
     */
    public function getAsConfiguration($path)
    {
        $result = $this->get($path);
        return new $this(is_array($result) ? $result : array());
    }

    /**
     * Set a value on the configuration by path
     * @param type $path The path to set the value on
     * @param type $value The value to set
     */
    public function set($path, $value)
    {
        $keys = \explode('.', $path);
        $arr = &$this->configuration;
        foreach ($keys as $key) {
            $arr = &$arr[$key];
        }
        $arr = $value;
    }
}
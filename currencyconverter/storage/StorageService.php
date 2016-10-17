<?php namespace Rickbrunken\CurrencyConverter\Storage;

use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of StorageService
 *
 * @author rick.brunken
 */
abstract class StorageService
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

    public abstract function install();

    public abstract function uninstall();

    public abstract function flush();
}
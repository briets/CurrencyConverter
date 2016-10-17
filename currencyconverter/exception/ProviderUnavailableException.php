<?php namespace Rickbrunken\CurrencyConverter\Exception;

use Exception;

/**
 * Description of ProviderUnavailableException
 *
 * @author rick.brunken
 */
class ProviderUnavailableException extends Exception
{
    public function __construct($provider = null)
    {
        if ($provider === null) {
            $provider = "'" . $provider . "' ";
        } else {
            $provider .= " ";
        }
        parent::__construct("Provider " . $provider . "is not available");
    }
}
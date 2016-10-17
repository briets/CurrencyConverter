<?php namespace Rickbrunken\CurrencyConverter\Exception;

use Exception;

/**
 * Description of CurrencyUnavailableException
 *
 * @author rick.brunken
 */
class UnavailableCurrencyException extends Exception
{

    public function __construct($currency)
    {
        parent::__construct("Currency '" . $currency . "' is not available");
    }
}
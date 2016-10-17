<?php namespace Rickbrunken\CurrencyConverter\Exception;

use Exception;

/**
 * Description of UnsupportedCurrencyException
 *
 * @author rick.brunken
 */
class UnsupportedCurrencyException extends Exception
{
    public function __construct($currency)
    {
        parent::__construct("Currency '" . $currency . "' is not supported");
    }

}
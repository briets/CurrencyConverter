<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of GraphData
 *
 * @author rick.brunken
 */

/**
 * @pw_set nillable=false
 * @pw_element string $Currency
 * @pw_set nillable=false
 * @pw_element string $DateTime
 * @pw_set nillable=false
 * @pw_element float $Rate
 * @pw_complex GraphData
 */
class GraphData
{
    public $Currency;
    public $DateTime;
    public $Rate;

    function __construct($Currency, $DateTime, $Rate)
    {
        $this->Currency = $Currency;
        $this->DateTime = $DateTime;
        $this->Rate = $Rate;
    }
}
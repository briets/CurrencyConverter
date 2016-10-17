<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of GetGraph
 *
 * @author rick.brunken
 *
 * @pw_set nillable=false
 * @pw_element string $Currency The currency to get data of
 * @pw_element string $FromDate Startdate of data
 * @pw_element string $ToDate Enddate of data
 * @pw_complex GetGraph The complex type name definition
 */
class GetGraph
{
    public $Currency;
    public $FromDate;
    public $ToDate;

}
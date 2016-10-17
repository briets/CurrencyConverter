<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of GetGraphResponse
 *
 * @author rick.brunken
 */

/**
 * @pw_set nillable=false
 * @pw_element GraphDataArray $GraphDataArray
 * @pw_complex GetGraphResponse The complex type name definition
 */
class GetGraphResponse
{
    /**
     *
     * @var GraphDataArray
     */
    public $GraphDataArray;

    function __construct()
    {
        $this->GraphDataArray = [];
    }


}
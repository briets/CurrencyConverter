<?php namespace Rickbrunken\CurrencyConverter\Soap;

/**
 * Description of Auth
 *
 * @author rick.brunken
 */
class Auth
{
    /**
     *
     * @var string
     */
    public $Username;

    /**
     *
     * @var string
     */
    public $Password;

    function __construct($Username, $Password)
    {
        $this->Username = $Username;
        $this->Password = $Password;
    }
}
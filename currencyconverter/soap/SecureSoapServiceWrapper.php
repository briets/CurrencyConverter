<?php namespace Rickbrunken\CurrencyConverter\Soap;

use Rickbrunken\CurrencyConverter\Soap\Auth;
use Rickbrunken\CurrencyConverter\Storage\Storage;
use soap_fault;

/**
 * Description of SecureSoapServiceWrapper
 *
 * @author rick.brunken
 */
class SecureSoapServiceWrapper
{
    protected $class;
    protected $authenticated = false;
    protected $user;

    function __construct($class_instance)
    {
        $this->class = $class_instance;
    }

    /**
     * Header Auth
     * @param Auth $Auth
     */
    public function Auth(Auth $Auth)
    {
        $this->user = Storage::getUserByUsernamePassword($Auth->Username, $Auth->Password);
        $this->authenticated = $this->user !== null;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->class, $method)) {
            return new soap_fault('[SOAP:Server]', null, 'method not found');
        }

        // Call the method on the class instance
        if ($this->authenticated) {
            Storage::storeSoapLog($this->user);
            return \call_user_method_array($method, $this->class, $args);
        } else {
            return new soap_fault('SOAP-ENV:Server', null, 'authentication failed');
        }
    }
}
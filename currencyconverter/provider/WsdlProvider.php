<?php namespace Rickbrunken\CurrencyConverter\Provider;

use Exception;
use Rickbrunken\CurrencyConverter\Configuration;
use SoapClient;

/**
 * Description of WsdlProvider
 *
 * @author rick.brunken
 */
abstract class WsdlProvider extends Provider
{
    /**
     * SoapClient to connect to webservice with
     * @var SoapClient
     */
    protected $soapClient = null;

    /**
     * The WSDL to connect the soapclient to
     * @var string
     */
    protected $wsdl = null;

    /**
     * Constructor
     * @param Configuration $configuration
     * @throws Exception Throws Exception if WSDL file is invalid
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        if (!\is_string($this->wsdl)) {
            throw new Exception('WSDL must be a valid WSDL URI');
        }
    }

    /**
     * Get a handle to the PHP SoapClient
     * @return SoapClient
     */
    protected function getSoapClient()
    {
        if ($this->soapClient === null) {
            try {
                $this->soapClient = new \SoapClient($this->wsdl);
            } catch (Exception $ex) {
                $this->setUnavailable($ex->getMessage());
            }
        }
        return $this->soapClient;
    }

    /**
     * Do a SOAP request and get the result
     * @param string $method Method name to invoke
     * @param array $arguments Arguments passed to soap call
     * @return type
     */
    protected function invokeMethod($method, $arguments)
    {
        try {
            return $this->getSoapClient()->__soapCall($method, array($method => $arguments));
        } catch (\Exception $ex) {
            $this->setUnavailable($ex->getMessage());
            throw $ex;
        }
    }
}
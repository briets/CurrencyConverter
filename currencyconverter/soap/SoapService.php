<?php namespace Rickbrunken\CurrencyConverter\Soap;

use Exception;
use Rickbrunken\CurrencyConverter\CurrencyConverter;
use Rickbrunken\CurrencyConverter\Soap\GetConversionRateResponse;
use soap_fault;

/**
 * Description of SoapService
 *
 * @author rick.brunken
 */
class SoapService
{
    protected $converter;

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     *
     * @pw_set nillable=false 
     * @param GetConversionRate $GetConversionRate
     * @return GetConversionRateResponse
     */
    public function GetConversionRate(GetConversionRate $GetConversionRate)
    {
        try {
            $response = new GetConversionRateResponse();
            $rates = $this->converter->getCurrencyRates([$GetConversionRate->FromCurrency, $GetConversionRate->ToCurrency]);
            $response->GetConversionRate = $rates[$GetConversionRate->ToCurrency]
                / $rates[$GetConversionRate->FromCurrency];
            return $response;
        } catch (Exception $ex) {
            return new soap_fault('SOAP-ENV:Server', null, $ex->getMessage());
        }
    }

    /**
     *
     * @pw_set nillable=false 
     * @param GetCurrencyRate $GetCurrencyRate
     * @return GetCurrencyRateResponse
     */
    public function getCurrencyRate(GetCurrencyRate $GetCurrencyRate)
    {
        try {
            $response = new GetCurrencyRateResponse();
            $rates = $this->converter->getCurrencyRates([$GetCurrencyRate->Currency]);
            $response->GetCurrencyRate = $rates[$GetCurrencyRate->Currency];
            return $response;
        } catch (Exception $ex) {
            return new soap_fault('SOAP-ENV:Server', null, $ex->getMessage());
        }
    }

    /**
     *
     * @param GetGraph $GetGraph
     * @return GetGraphResponse
     */
    public function getGraph(GetGraph $GetGraph)
    {
        try {
            $response = new GetGraphResponse();
            $graph = $this->converter->getGraphData(
                $GetGraph->Currency, \DateTime::createFromFormat('Y-m-d', $GetGraph->FromDate), \DateTime::createFromFormat('Y-m-d', $GetGraph->ToDate));
            foreach ($graph as $date => $rate) {
                $response->GraphDataArray[] = new GraphData($GetGraph->Currency, $date, $rate);
            }
            return $response;
        } catch (Exception $ex) {
            return new soap_fault('SOAP-ENV:Server', null, $ex->getMessage());
        }
    }
}
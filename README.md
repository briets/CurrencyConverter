# CurrencyConverter

CurrencyConcerter provides basic Webservice functionality to handle SOAP requests for Currency converting. 

## Provides
CurrencyConverter provides the following functionality:
- Caching
- Usage of multiple data providers
- Lowest costing provider will be used if  possible
- Basic SOAP Header Authentication

## Install
To install the currencyconverter you need to:
 - Download this repo
 - Setup configuration: edit configuration.php to your needs
 - Execute commands in CLI
```json
composer install
```
```json
php cli.php install
php cli.php adduser <username> <password>
```

## Collecting needed data
To ensure all data is available as needed (for graph data) you need to run a cron task which will fetch data as needed.You do this by calling 
the following script every minute in a CLI:
```json
php cli.php cron
```
## Usage
The webservice currently supports 3 methods:
 - GetConversionRate => Get a conversionrate between 2 currencies
 - GetCurrencyRate => Get a currency rate
 - GetGraph => Get currencyrates over a period of time per day

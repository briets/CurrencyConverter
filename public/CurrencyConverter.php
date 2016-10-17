<?php
require '../vendor/autoload.php';

// Create the configuration
$configuration = new Rickbrunken\CurrencyConverter\Configuration(require('../configuration.php'));

// Create the CurrencyConverter
$currencyConverter = new Rickbrunken\CurrencyConverter\CurrencyConverter($configuration);

// Handle the command
$currencyConverter->handleHttpCommand();

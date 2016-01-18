# CmPayService
CmPayService is a Laravel package for the API of CM Payments. (pay.cm.nl)

# How to use it:
## composer
Require it:

- "CJSDevelopment/CmPayService" : "dev-master"

## app.php
Add the following data:
### ServiceProvider
- 'CJSDevelopment\CmPayServiceProvider'

### Facade
- 'PaymentService' => 'CJSDevelopment\CmPayService',

## Deploy / Update
Execute the following commands in your console:
- sudo composer selfupdate
- sudo composer update
- sudo php artisan vendor:publish

# Config
After executing the vendor:publish command you will find a config file in the Laravel config folder. Fill the Mandatory fields, check the optional.

Don't forget to fill in the right company name and product token

# Usage in your code:
## Initializing
For payment methods & options:
- CmPayService::getPaymentMethods($amount (should be integer));

## Executing
To forward the customer to the payment screen of their financial instance.
- CmPayService::getTransactionUrl($parameters);

## Check
- CmPayService::checkPayment(Request::post);
$parameters should contain at least amount, but can hold more options.


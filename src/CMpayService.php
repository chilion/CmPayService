<?php

namespace CJSDevelopment;

use SimpleXMLElement;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

define("paymentUrl", "https://pay.cm.nl/API/v3/getTransactionUrl");
define("methodsUrl", "https://pay.cm.nl/API/v3/getPaymentMethods");

class CMpayService
{
    /**
     * @param $amount
     *
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public static function getPaymentMethods($amount)
    {
        // Standard data
        $sendObject = self::mandatoryData();

        // Optional or Flexible Data
        $sendObject['Amount']     = $amount;

        // Hash Calculation
        $sendObject['Hash']       = self::calculateHash($sendObject);

        // Get results
        $resultSet = self::transferData($sendObject, $method = 1);

        return $resultSet;
    }

    public static function getTransactionUrl($parameters) {

        // Standard data
        $sendObject = self::mandatoryData();

        // Optional or Flexible Data
        $sendObject['Amount']               = $parameters['amount'];
        $sendObject['Reference']            = (array_key_exists("reference", $parameters)) ? $parameters["reference"] : config('cmpayservice.reference_prefix', "your order with"). " " .config('cmpayservice.company', "company");

        // Return Url's
        $sendObject["SuccessURL"]           = "http://example.com";
        $sendObject["FailURL"]              = "http://example.com";
        $sendObject["ErrorURL"]             = "http://example.com";
        $sendObject["CancelURL"]            = "http://example.com";

        // Payment data
        $sendObject['PaymentMethod']        = "IDEAL";
        $sendObject['PaymentMethodOption']  = "ABNANL2A";

        // Hash Calculation
        $sendObject['Hash']                 = self::calculateHash($sendObject);

        // Get results
        return self::transferData($sendObject);

    }

    public static function checkPayment($parameters) {

    }

    /**
     * Not Reachable functions. Should not, will not, be not, can not. Well, of course you can, because everything can. Coffee can. Thats a Dutch joke. Totally not working in English.
     *
     * Allright, this comment block was just to make a line between public accessible methods and private(ers)!
     *
     */


    /**
     * @param array $options
     *
     * @return string
     */
    private static function calculateHash(array $options = [])
    {
        // Add standard options to the Hash Calculation.

        // Test?
        if (env('APP_DEBUG')) {

            $options['Test'] = '1';
        }

        ksort($options);

        $options['Secret'] = config('cmpayservice.secret');

        $hashString = '';
        foreach ($options as $key => $value) {

            $hashString .= $key . '=' . $value . ',';
        }

        return hash('sha256', rtrim($hashString, ','));
    }

    /**
     * @param $data
     *
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    private static function transferData($data, $method = null)
    {
        // Test?
        if (env('APP_DEBUG')) {

            $data['Test'] = '1';
        }

        $dataTransfer = new Client();

        $dT = $dataTransfer->post(($method != null) ? methodsUrl : paymentUrl, ['json' => $data]);

        if ($dT->getStatusCode() != 200) {
            return false;
        }

        return $dT->getBody()->getContents();
    }

    private static function mandatoryData() {
        $mandatoryData['MerchantID']           = config('cmpayservice.merchant_id');
        $mandatoryData['Currency']             = config('cmpayservice.currency');
        $mandatoryData['Language']             = config('cmpayservice.language');
        $mandatoryData['Country']              = config('cmpayservice.country');

        return $mandatoryData;
    }

}

<?php

namespace CJSDevelopment;

use GuzzleHttp\Client;
use Illuminate\Http\Request;


define('paymentUrl', 'https://pay.cm.nl/API/v3/getTransactionUrl');
define('methodsUrl', 'https://pay.cm.nl/API/v3/getPaymentMethods');

class CmPayService
{
    /**
     * getPaymentMethods.
     *
     * @since 01-2016
     *
     * @author Chilion Snoek <chilionsnoek@gmail.com>
     *
     * @param $amount
     *
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public static function getPaymentMethods($amount)
    {
        // Standard data
        $sendObject = self::mandatoryData();

        // Optional or Flexible Data
        $sendObject['Amount'] = $amount;

        // Hash Calculation
        $sendObject['Hash'] = self::calculateHash($sendObject);

        // Get results
        $resultSet = self::transferData($sendObject, $method = 1);

        $return = json_decode($resultSet, true);

        return $return["paymentMethods"];
    }

    /**
     * getTransactionUrl gets the Transaction Url from the API.
     *
     * @since 01-2016
     *
     * @author Chilion Snoek <chilionsnoek@gmail.com>
     *
     * @param $amount The amount
     * @param $method Payment Method (iDeal mostly)
     * @param $option Payment Option (Bank Code)
     * @param $parameters
     *
     * @return bool|string
     */
    public static function getTransactionUrl($amount, $method, $option, array $parameters = null)
    {

        // Standard data
        $sendObject = self::mandatoryData();

        // Optional or Flexible Data
        $sendObject['Amount'] = $amount;
        $sendObject['Reference'] = (array_key_exists('reference', $parameters)) ? $parameters['reference'] : config('cmpayservice.reference_prefix', 'your order with').' '.config('cmpayservice.company', 'company');

        // Return Url's
        $sendObject['SuccessURL'] = (array_key_exists('SuccessURL', $parameters)) ? $parameters['SuccessURL'] : config('cmpayservice.return_url').config('cmpayservice.success_url');
        $sendObject['FailURL'] = (array_key_exists('FailURL', $parameters)) ? $parameters['FailURL'] : config('cmpayservice.return_url').config('cmpayservice.fail_url');
        $sendObject['ErrorURL'] = (array_key_exists('ErrorURL', $parameters)) ? $parameters['ErrorURL'] : config('cmpayservice.return_url').config('cmpayservice.error_url');
        $sendObject['CancelURL'] = (array_key_exists('CancelURL', $parameters)) ? $parameters['CancelURL'] : config('cmpayservice.return_url').config('cmpayservice.cancel_url');

        // Payment data
        $sendObject['PaymentMethod'] = $method;
        $sendObject['PaymentMethodOption'] = $option;

        // Hash Calculation
        $sendObject['Hash'] = self::calculateHash($sendObject);

        // Get results and return given URL
        return self::transferData($sendObject);
    }

    public static function checkPayment(Request $request, array $parameters = null)
    {
        dd($request->all());
    }













    /**
     * Not Reachable functions. Should not, will not, be not, can not. Well, of course you can, because everything can. Coffee can. Thats a Dutch joke. Totally not working in English.
     *
     * Allright, this comment block was just to make a line between public accessible methods and private(ers)!
     */

    /**
     * calculateHash calculates the Hash to encrypt your request with.
     *
     * @since 01-2016
     *
     * @author Chilion Snoek <chilionsnoek@gmail.com>
     *
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
            $hashString .= $key.'='.$value.',';
        }

        return hash('sha256', rtrim($hashString, ','));
    }

    /**
     * transferData to the API method.
     *
     * @since 01-2016
     *
     * @author Chilion Snoek <chilionsnoek@gmail.com>
     *
     * @param $data
     * @param null $method
     *
     * @return bool|string
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

    /**
     * mandatoryData returns all mandatory data for each request.
     *
     * @since 01-2016
     *
     * @author Chilion Snoek <chilionsnoek@gmail.com>
     *
     * @return mixed
     */
    private static function mandatoryData()
    {
        $mandatoryData['MerchantID'] = config('cmpayservice.merchant_id');
        $mandatoryData['Currency'] = config('cmpayservice.currency');
        $mandatoryData['Language'] = config('cmpayservice.language');
        $mandatoryData['Country'] = config('cmpayservice.country');

        return $mandatoryData;
    }
}

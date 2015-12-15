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
        $sendObject['MerchantID'] = config('cmpayservice.merchant_id');
        $sendObject['Amount']     = $amount;
        $sendObject['Currency']   = config('cmpayservice.currency');
        $sendObject['Language']   = config('cmpayservice.language');
        $sendObject['Country']    = config('cmpayservice.country');
        $sendObject['Hash']       = self::_calculateHash(array_merge($sendObject, ['Amount' => $amount]));

        // Test?
        if (env('APP_DEBUG')) {

            $sendObject['Test'] = '1';
        }

        $resultSet = self::transferData($sendObject);

        return $resultSet;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private static function _calculateHash(array $options = [])
    {
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
    private static function transferData($data)
    {
        $dataTransfer = new Client();

        $dT = $dataTransfer->post(methodsUrl, ['json' => $data]);

        if ($dT->getStatusCode() != 200) {
            return false;
        }

        return $dT;
    }
}

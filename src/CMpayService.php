<?php

namespace CJSDevelopment;

use SimpleXMLElement;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

define("paymentUrl", "https://pay.cm.nl/API/v3/getTransactionUrl");
define("methodsUrl", "https://pay.cm.nl/API/v3/getPaymentMethods");

class CMpayService
{
    public static function transferData($data) {

        $dataTransfer = new Client();

        ksort($data);

        dd(json_encode($data));
        $dT = $dataTransfer->post(methodsUrl, ['json' => [json_encode($data)]]);

        if ($dT->getStatusCode() != 200) {
            return false;
        }

        //dd($dT);
        return $dT;
    }

    public static function getPaymentMethods($amount = "1") {
        $sendObject["Amount"] = $amount;
        $sendObject["MerchantID"] = config('cmpayservice.merchant_id');
        $sendObject["Currency"] = config('cmpayservice.currency');
        $sendObject["Language"] = config('cmpayservice.language');
        $sendObject["Country"] = config('cmpayservice.country');
        $sendObject["Secret"] = config('cmpayservice.secret');
        $sendObject["Hash"] = self::_calculateHash(["Amount" => $amount]);

        $resultSet = self::transferData($sendObject);

        return $resultSet;
    }

    private static function _calculateHash(array $hashArray = null) {

        $hashString = "";

        // First build the hash array
        $hashArray["MerchantID"] = config('cmpayservice.merchant_id');
        $hashArray["Currency"] = config('cmpayservice.currency');
        $hashArray["Language"] = config('cmpayservice.language');
        $hashArray["Country"] = config('cmpayservice.country');
        $hashArray["Secret"] = config('cmpayservice.secret');

        // Add in extra components
        // Test?
        if(env("APP_DEBUG")) {
            $hashArray["Test"] = 1;
        }

        ksort($hashArray);

        foreach($hashArray as $key => $value) {
            $hashString .= $key."=".$value.",";
        }

        //dd(rtrim($hashString, ","));
        return hash("sha256", rtrim($hashString, ","));
    }



}

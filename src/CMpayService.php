<?php

namespace CJSDevelopment;

use SimpleXMLElement;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CMpayService
{
    protected $paymentUrl;
    protected $methodsUrl;

    public function __construct()
    {
        $this->paymentUrl = "https://pay.cm.nl/API/v3/getTransactionUrl";
        $this->methodsUrl = "https://pay.cm.nl/API/v3/getPaymentMethods ";

        return $this;
    }

    public function getMethodsUrl() {
        return $this->methodsUrl;
    }

    public function getPaymentUrl() {
        return $this->paymentUrl;
    }

    public static function transferData($data) {

        $dataTransfer = new Client();

        $dT = $dataTransfer->post(self::getMethodsUrl(), ['json' => [$data]]);

        return $dT;
    }

    public static function getPaymentMethods($amount = null) {
        $jsonObject = [
            "MerchantID:".config('cmpayservice.merchant_id'),
            "Currency:".config('cmpayservice.currency', "EUR"),
            "Language:".config('cmpayservice.language', "nl"),
            "Country:".config('cmpayservice.country', "nl"),
            "Amount:".(is_null($amount) ? "0.01" : $amount),

            "Hash:".self::_calculateHash(["Amount" => $amount])
        ];

        $resultSet = self::transferData($jsonObject);


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

        return hash("sha256", $hashString);
    }



}

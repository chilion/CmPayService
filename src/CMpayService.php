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
            "Hash:".self::_calculateHash()
        ];

        $resultSet = self::transferData($jsonObject);


    }

    private function _calculateHash($amount = null) {

        // Add in standard hash components
        $hashString = "MerchantID=".config('cmpayservice.merchant_id').",Currency=".config('cmpayservice.currency', "EUR").",Language:".config('cmpayservice.language', "nl").",Country:".config('cmpayservice.country', "nl").",";

        // Add in extra components

            // Is there an amount set?
            $hashString .= (is_null($amount) ? "0,01" : $amount);

            // Return URL?







            // Test?
            $hashString .= (env("APP_DEBUG") == TRUE ? 1 : 0);




        return hash("sha256", $hashString);
    }



}

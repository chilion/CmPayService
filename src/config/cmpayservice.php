<?php

return [

    // Mandatory
    'merchant_id'       => 'use-your-own',
    'secret'            => 'use-your-own',
    'country'           => 'nl',
    'language'          => 'nl',
    'currency'          => 'EUR',

    // Optional (also fillable within your code)
    'company'           => 'your company name',
    'reference_prefix'  => 'your reference prefix',

    // Optional (checks for Laravel settings to)
    'debug'             => false,
];

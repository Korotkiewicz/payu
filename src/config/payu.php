<?php

return [

    /*
    | (Boolean) Determines if Openpay is running in production mode.
    */
    'production_mode' => env('PAYU_PRODUCTION_MODE'),

    /*
    | Your Merchant ID, found in Openpay Dashboard -- Configuration.
    */
    'merchant_id' => env('PAYU_METCHANT_ID'),

    /*
    | Your Private Key, found in Openpay Dashboard -- Configuration.
    */
    'signature_key' => env('PAYU_SIGNATURE_KEY'),

    'client_id' => env('PAYU_CLIENT_ID'),

    'client_secret' => env('PAYU_CLIENT_SECRET'),
];

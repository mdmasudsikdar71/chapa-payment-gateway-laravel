<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chapa API Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to authenticate your requests to the Chapa API. You can
    | obtain your secret key from the Chapa dashboard. Make sure to keep this
    | key secure and do not expose it publicly.
    |
    */

    'secret_key' => env('CHAPA_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Chapa Transaction Reference (tx_ref) Prefix
    |--------------------------------------------------------------------------
    |
    | If provided, this prefix will be appended to the 'tx_ref' field when
    | initiating transactions. It can be used to uniquely identify transactions.
    | Leave it empty if you don't want to use a prefix.
    |
    */

    'tx_ref_prefix' => env('CHAPA_TX_REF_PREFIX', ''),
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Test Activa
    |--------------------------------------------------------------------------
    |
    | Testna kartica za activa gateway: 5450429876543211
    | Ppoljuben cvc, veljavnost v prihodnosti.
    | Kartica ima limit, tako da je zaželjeno,
    | da se testira z nizkimi zneski.
    |
    */

    'environment' => env('MEGAPOS_ENV'),
    'cert' => env('MEGAPOS_CERT'),
    'gateway_ids' =>  env('MEGAPOS_GATEWAY_IDS'),
    'store_id' => env('MEGAPOS_STORE_ID'),
    'redirect' => env('MEGAPOS_REDIRECT', true),

    /*
    |--------------------------------------------------------------------------
    | Testing
    |--------------------------------------------------------------------------
    |
    | disable in production
    |
    */

    'enable_test_routes' => env('MEGAPOS_ENABLE_TEST_ROUTES', true),
    'debug' => env('MEGAPOS_DEBUG', false),

];
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações da API CNPJ.WS
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a API CNPJ.WS
    |
    */
    'cnpjws' => [
        'token' => env('CNPJWS_TOKEN', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações da Brasil API
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a Brasil API
    | Atualmente não requer autenticação
    |
    */
    'brasilapi' => [
        // A Brasil API não requer autenticação
    ],
];
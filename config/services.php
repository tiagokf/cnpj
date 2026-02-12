<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações da API CNPJ.WS
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a API CNPJ.WS
    | Token opcional para usar a API comercial com limites maiores
    |
    */
    'cnpjws' => [
        'token' => env('CNPJWS_TOKEN', null),
    ],
];

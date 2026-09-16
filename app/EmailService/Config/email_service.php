<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Keys (multi-consumidor)
    |--------------------------------------------------------------------------
    */
    'keys' => [
        'das_febnet' => [
            'key'          => env('EMAIL_API_KEY_DAS'),
            'label'        => 'DAS Guillon Ribeiro',
            'from_address' => env('EMAIL_API_KEY_DAS_FROM', 'administrator@fazcomphp.com.br'),
            'from_name'    => env('EMAIL_API_KEY_DAS_FROM_NAME', 'DAS Guillon Ribeiro'),
        ],

        // Novos consumidores entram aqui
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates permitidos
    |--------------------------------------------------------------------------
    */
    'allowed_templates' => [
        'teste_basico',
        'recuperacao_senha',
        'codigo_2fa',
        'troca_senha_obrigatoria',
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites
    |--------------------------------------------------------------------------
    */
    'max_recipients_per_request' => 1,
    'max_dados_size_kb'          => 50,
];
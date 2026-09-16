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
            'label'        => 'FEB Guillon Ribeiro',
            'from_address' => env('EMAIL_API_KEY_DAS_FROM', 'administrator@fazcomphp.com.br'),
            'from_name'    => env('EMAIL_API_KEY_DAS_FROM_NAME', 'FEB Guillon Ribeiro'),

            // 🔥 NOVO: branding do e-mail
            'brand_name'   => 'FEB Guillon Ribeiro',
            'brand_url'    => 'https://das.febnet.org.br',
            'brand_footer' => 'Federação Espírita Brasileira — Núcleo Espírita Guillon Ribeiro',
        ],

        // Próximos consumidores herdam o mesmo padrão
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
        'troca_senha_realizada',   // ← adicionar
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites
    |--------------------------------------------------------------------------
    */
    'max_recipients_per_request' => 1,
    'max_dados_size_kb'          => 50,
];
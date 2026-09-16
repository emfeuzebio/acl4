<?php

namespace App\EmailService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/email_service.php',
            'email_service'
        );
    }

    public function boot(): void
    {
        // Views do módulo (namespace 'emailservice')
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'emailservice');

        // Rotas com prefixo /api
        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/Routes/api.php');

        // Alias do middleware
        $this->app['router']->aliasMiddleware(
            'email.api.key',
            \App\EmailService\Http\Middleware\ApiKeyMiddleware::class
        );

        // Publica a config (opcional, para deploy)
        $this->publishes([
            __DIR__ . '/Config/email_service.php' => config_path('email_service.php'),
        ], 'email-service-config');
    }
}
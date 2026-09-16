<?php

namespace App\Providers;

use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\OrganizationRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;    

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
    }

    public function boot(): void
    {
        // 🔥 View Composer para os layouts de e-mail do Laravel
        // Injeta as variáveis de branding sempre que o layout externo for renderizado
        View::composer([
            'mail::html.message',
            'mail::text.message',
        ], function ($view) {
            $consumer = config('email_service.current_consumer', []);

            $view->with([
                'brand_name'   => $consumer['brand_name']   ?? config('app.name'),
                'brand_url'    => $consumer['brand_url']    ?? config('app.url'),
                'brand_footer' => $consumer['brand_footer'] ?? config('app.name'),
            ]);
        });
    }    

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     //
    // }
}

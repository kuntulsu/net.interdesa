<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('routeros', function () {
            return Http::withBasicAuth(config("routeros.username"), config("routeros.password"))->baseUrl('http://'.config("routeros.host").":".config("routeros.port")."/rest");
        });
    }
}

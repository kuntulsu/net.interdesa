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
            $http_scheme = config("routeros.ssl") ? "https" : "http";
            return Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->timeout(5)
                ->withBasicAuth(config("routeros.username"), config("routeros.password"))
                ->baseUrl("{$http_scheme}://".config("routeros.host").":".config("routeros.port")."/rest");
        });
    }
}

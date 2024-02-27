<?php

namespace MDMasudSikdar\Chapa;

use Illuminate\Support\ServiceProvider;

class ChapaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind ChapaClient to the container
        $this->app->bind(ChapaClient::class, function () {
            return new ChapaClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing configuration file
        $this->publishes([
            __DIR__ . '/../config/chapa.php' => config_path('chapa.php'),
        ], 'chapa-config');
    }
}

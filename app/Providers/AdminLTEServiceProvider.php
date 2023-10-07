<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AdminLTEServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish AdminLTE assets
        $this->publishes([
            'vendor/almasaeed2010/adminlte' => public_path('vendor/adminlte'),
        ], 'adminlte-assets');

        // // Optionally, you can merge AdminLTE configuration files
        // $this->mergeConfigFrom(
        //     __DIR__ . '/vendor/almasaeed2010/adminlte/config/adminlte.php',
        //     'adminlte'
        // );
    }
}

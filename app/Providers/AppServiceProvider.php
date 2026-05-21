<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\Pago;           // IMPORTANTE
use App\Observers\PagoObserver; // IMPORTANTE
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      Pago::observe(PagoObserver::class);
    }
}

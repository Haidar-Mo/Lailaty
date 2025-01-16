<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Transportation\TransportTaxiService;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*$this->app->bind(TransportTaxiService::class, function ($app) {
            return new TransportTaxiService($app->make(Database::class));
        });*/

        $this->app->singleton(Database::class, function ($app) {
            $firebase = (new Factory())
                ->withServiceAccount(base_path('storage/app/firebase/firebase_credentials.json')) 
                ->withDatabaseUri('https://al-nada-8cd85-default-rtdb.europe-west1.firebasedatabase.app/');
    
            return $firebase->createDatabase();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

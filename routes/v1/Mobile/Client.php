<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\Mobile\Client\TransportationController;
use Illuminate\Support\Facades\Route;


Route::prefix('client/')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value,
        //'role:client',

    ])
    ->group(function () {

        Route::prefix('transports')
            ->group(function () {

                Route::post('order/{serviceType}', [TransportationController::class, 'orderService']);
            });

    });

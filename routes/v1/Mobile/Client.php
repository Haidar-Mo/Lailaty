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
                Route::post('update/price/{serviceType}/{id}', [TransportationController::class, 'updateOrder']);
                Route::post('update/auto-accept/{boolean}/{serviceType}/{id}', [TransportationController::class, 'updateAutoAccept']);
                Route::post('cancel/{serviceType}/{id}', [TransportationController::class, 'cancelOrder']);

            });

    });

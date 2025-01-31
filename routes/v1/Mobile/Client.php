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
                Route::post('cancel/order/{serviceType}/{id}', [TransportationController::class, 'CancelOrderService']);

                Route::post('accept/order/{serviceType}/{id}', [TransportationController::class, 'acceptTransportOrder']);
                Route::get('order/{serviceType}', [TransportationController::class, 'getOrderOfferTransport']);
                Route::post('accept/order/offer/{serviceType}/{id}', [TransportationController::class, 'acceptOrderOfferTransport']);
                Route::post('update/order/offer/{serviceType}/{id}', [TransportationController::class, 'updateOrderOffer']);
            });

    });

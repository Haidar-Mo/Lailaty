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

                Route::post('cancel/order/{serviceType}/{id}', [TransportationController::class, 'cancelOrder']);

                Route::post('accept/order/offer/{serviceType}/{id}', [TransportationController::class, 'acceptOrderOfferTransport']);
                Route::get('order/{serviceType}', [TransportationController::class, 'getOrderOfferTransport']);

                Route::post('update/order/offer/{serviceType}/{id}', [TransportationController::class, 'updateOrderOffer']);
                Route::post('subscription/order/offer/{serviceType}/{id}', [TransportationController::class, 'subscriptionOrder']);
                Route::post('cancel/order/offer/{serviceType}/{id}', [TransportationController::class, 'cancelOrderOffer']);





            });

    });

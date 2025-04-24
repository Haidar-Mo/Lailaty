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

                Route::post('order/{serviceType}', [TransportationController::class, 'createOrder']);
                Route::post('update/price/{serviceType}/{id}', [TransportationController::class, 'updatePriceOrder']);
                Route::post('update/auto-accept/{boolean}/{serviceType}/{id}', [TransportationController::class, 'updateAutoAcceptOrder']);
                Route::post('cancel/{serviceType}/{id}', [TransportationController::class, 'cancelOrder']);



                Route::post('cancel/order/{serviceType}/{id}', [TransportationController::class, 'cancelOrder']);

                Route::post('accept/order/offer/{serviceType}/{id}', [TransportationController::class, 'acceptOrderOfferTransport']);
                Route::get('order/offer/{serviceType}', [TransportationController::class, 'getOrderOfferTransport']);
                Route::get('order/{serviceType}', [TransportationController::class, 'getOrderTransport']);
                Route::get('show/order/offer/{serviceType}/{id}', [TransportationController::class, 'showOffer']);
                Route::post('update/order/offer/{serviceType}/{id}', [TransportationController::class, 'updateOrderOffer']);
                Route::post('subscription/order/offer/{serviceType}/{id}', [TransportationController::class, 'subscriptionOrder']);
                Route::post('cancel/order/offer/{serviceType}/{id}', [TransportationController::class, 'cancelOrderOffer']);

            });

    });

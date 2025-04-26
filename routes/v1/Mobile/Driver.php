<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\Mobile\Driver\VehicleController;
use App\Http\Controllers\Api\Mobile\Driver\VehicleServiceRegisterController;
use App\Http\Controllers\Api\Mobile\Driver\VehicleWorkRequestController;
use App\Http\Controllers\Api\Mobile\Driver\TransportationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\OfficeRegistrationController;


Route::prefix('captain/')
    ->middleware([
         'auth:sanctum',
         'ability:' . TokenAbility::ACCESS_API->value,
        // 'role:fleetOwner|freeDriver|employeeDriver',

    ])
    ->group(function () {

        Route::prefix('offices/')
            // ->middleware('can:create-office')
            ->group(function () {
                Route::get('show', [OfficeRegistrationController::class, 'show']);
                Route::post('create/{type}', [OfficeRegistrationController::class, 'store']);

                Route::post('document/update', [OfficeRegistrationController::class, 'updateOfficeDocument']);

            });

        Route::prefix('vehicles/')
            // ->middleware('can-use-vehicle')
            ->group(function () {

                Route::get('index', [VehicleController::class, 'index']);
                Route::get('show/{id}', [VehicleController::class, 'show']);
                Route::post('create/{vehicleType}', [VehicleController::class, 'store']);
                Route::post('update/{vehicleType}/{id}', [VehicleController::class, 'update']);
                Route::post('update/{vehicleId}/image/{imageId}', [VehicleController::class, 'updateImage']);
                Route::post('update/{id}/document/ownership', [VehicleController::class, 'updateOwnershipDocument']);

                Route::get('brand', [VehicleController::class, 'indexBrand']);
            });

        Route::prefix('services/')
            //->middleware()
            ->group(function () {

                Route::post('register/{id}', [VehicleServiceRegisterController::class, 'store']);

            });


        Route::prefix('works/')
            //->middleware('')
            ->group(function () {

                Route::get('fleet-owner/index', [VehicleWorkRequestController::class, 'indexFleetOwner']);
               // Route::get('search', [VehicleWorkRequestController::class, 'searchFleetOwner']);

                Route::get('fleet-owner/{id}/vehicle/index', [VehicleWorkRequestController::class, 'indexAvailableVehicle']);

                Route::post('request/create/{id}', [VehicleWorkRequestController::class, 'create']);
            });



        Route::prefix('orders/')
            //->middleware()
            ->group(function () {
                Route::get('get/order/pending/{serviceType}', [TransportationController::class, 'getOrderTransport']);
                Route::post('accept/{serviceType}/{id}', [TransportationController::class, 'acceptOrder']);
                Route::post('cancel/{serviceType}/{id}', [TransportationController::class, 'cancelTransportOrder']);
                Route::post('update/order/offer/{serviceType}/{id}', [TransportationController::class, 'updateOrder']);
                Route::post('end/order/{serviceType}/{id}', [TransportationController::class, 'finishTransportOrder']);

            });
    });

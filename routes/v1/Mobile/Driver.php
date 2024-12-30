<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\Mobile\Driver\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\OfficeRegisterationController;


Route::prefix('captain/')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value,
        'role:fleetOwner|freeDriver|employeeDriver',

    ])
    ->group(function () {

        Route::prefix('offices/')
            // ->middleware('can:create-office')
            ->group(function () {
                Route::get('show', [OfficeRegisterationController::class, 'show']);

                Route::post('create', [OfficeRegisterationController::class, 'store']);

                Route::post('document/create', [OfficeRegisterationController::class, 'storeOfficeDocument']);

                Route::post('document/update', [OfficeRegisterationController::class, 'updateOfficeDocument']);

            });

        Route::prefix('vehicles/')
            // ->middleware('can-use-vehicle')
            ->group(function () {

                Route::get('index', [VehicleController::class, 'index']);
                Route::get('show', [VehicleController::class, 'show']);
                Route::post('create/{vehicleType}', [VehicleController::class, 'store']);

                
            });


    });

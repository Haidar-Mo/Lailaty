<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\OfficeRegisterationController;


Route::prefix('captain/')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value,
        'role:fleetOwner|freeDriver|employeeDriver'
    ])
    ->group(function () {

        Route::prefix('offices/')
            ->middleware('CanCreateOffice')
            ->group(function () {
                Route::get('show', [OfficeRegisterationController::class, 'show']);

                Route::post('create', [OfficeRegisterationController::class, 'store']);

                Route::post('document/create', [OfficeRegisterationController::class, 'storeOfficeDocument']);

                Route::post('document/update', [OfficeRegisterationController::class, 'updateOfficeDocument']);

            });

            
    });

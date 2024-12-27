<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\OfficeRegisterationController;


Route::prefix('captain')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ])
    ->group(function () {
        Route::middleware(['role:officeOwner'])
            ->get('offices/show', [OfficeRegisterationController::class, 'show']);

        Route::middleware(['role:officeOwner'])
            ->post('offices/create', [OfficeRegisterationController::class, 'store']);

        Route::middleware(['role:officeOwner'])
            ->post('offices/document/create', [OfficeRegisterationController::class, 'storeOfficeDocument']);
        
            Route::middleware(['role:officeOwner'])
            ->post('offices/document/update', [OfficeRegisterationController::class, 'updateOfficeDocument']);

    });

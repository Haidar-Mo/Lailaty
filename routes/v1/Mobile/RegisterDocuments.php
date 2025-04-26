<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\DocumentsRegisterController;


Route::prefix('captain')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value,
        'role:freeDriver|employeeDrive|fleetOwner'
    ])
    ->group(function () {
        Route::apiResource('Documents', DocumentsRegisterController::class);

        Route::post('Documents/update', [DocumentsRegisterController::class, 'update']);
        Route::get('document/check', [DocumentsRegisterController::class, 'check']);
    });

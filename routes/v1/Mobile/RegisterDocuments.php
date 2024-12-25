<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\DocumentsRegisterController;


Route::prefix('captain')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ])
    ->group(function () {
        Route::middleware('isCaptain')->apiResource('Documents',DocumentsRegisterController::class);
        Route::middleware(['isCaptain'])->post('Documents/update', [DocumentsRegisterController::class, 'update']);
    });

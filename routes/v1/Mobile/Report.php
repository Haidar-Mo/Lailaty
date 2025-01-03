<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\ReportsController;
use App\Http\Controllers\Api\Mobile\RateController;

Route::prefix('Mobile')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ])
    ->group(function () {

        Route::apiResource('reports',ReportsController::class);
        


    });

<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\RateController;

Route::prefix('Mobile')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ])
    ->group(function () {
        Route::middleware(['role:freeDriver|employeeDriver'])
        ->post('rate/client/{id}',[RateController::class,'RateClient']);
        Route::post('rate/car/{id}',[RateController::class,'RateCar']);

    });

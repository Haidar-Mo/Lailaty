<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Driver\RegisterationStuffController;


Route::prefix('captain')
    ->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ])
    ->group(function () {
        Route::middleware(['role:officeOwner'])->post('office/create', [RegisterationStuffController::class, 'officeRegister']);

    });
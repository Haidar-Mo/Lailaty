<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\Mobile\ProfileController;


Route::prefix('profile')->middleware([
    'auth:sanctum',
    'ability:' . TokenAbility::ACCESS_API->value
])->group(function () {

    Route::get('show',[ProfileController::class,'show']);
    Route::post('newProfileImage', [ProfileController::class, 'newProfileImage']);
    Route::post('removeProfileImage', [ProfileController::class, 'removeProfileImage']);
});
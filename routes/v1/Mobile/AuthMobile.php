<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\Mobile\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Auth\AuthController;
use App\Http\Controllers\Api\Mobile\Auth\ResetPasswordController;


Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::post('emailRegistration', [RegistrationController::class, 'Register']);
    Route::post('verifyEmail', [RegistrationController::class, 'verifyEmail']);
    Route::post('resendVerificationCode', [RegistrationController::class, 'resendVerificationCode']);
    Route::post('informationRegistration', [RegistrationController::class, 'informationRegistration'])->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ]);

    Route::post('forgot-password', [ResetPasswordController::class, 'sendResetLink'])->name('password.request');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');

    Route::post('refreshToken', [AuthController::class, 'refreshToken'])->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value
    ]);

    Route::post('logout', [AuthController::class, 'logout'])->middleware([
        'auth:sanctum',
        'ability:' . TokenAbility::ACCESS_API->value
    ]);

});
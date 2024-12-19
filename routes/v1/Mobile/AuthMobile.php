<?php

use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\Auth\AuthController;
use App\Http\Controllers\Api\Mobile\Auth\ResetPasswordController;


Route::group(["prefix" => "auth",], function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::post('emailRegistration', [AuthController::class, 'emailRegistration']);
    Route::post('verifyEmail', [AuthController::class, 'verifyEmail']);
    Route::post('resendVerificationCode', [AuthController::class, 'resendVerificationCode']);
    Route::post('informationRegistration', [AuthController::class, 'informationRegistration']);

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
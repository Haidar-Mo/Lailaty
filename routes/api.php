<?php

use App\Enums\TokenAbility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    'ability:' . TokenAbility::ACCESS_API->value,
])->
    get('/auth/check-token', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'user' => $request->user(),
        ]);
    });
Route::prefix('v1/')->group(function () {

    include __DIR__ . "/v1/Mobile/AuthMobile.php";
    include __DIR__ . "/v1/Mobile/Profile.php";
    include __DIR__ . "/v1/Mobile/Client.php";
    include __DIR__ . "/v1/Mobile/Driver.php";
    include __DIR__ . "/v1/Mobile/RegisterDocuments.php";
    include __DIR__ . "/v1/Mobile/Report.php";
    include __DIR__ . "/v1/Mobile/Rate.php";
});
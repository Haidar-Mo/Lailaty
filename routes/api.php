<?php

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

include __DIR__ . "/v1/Mobile/AuthMobile.php";
include __DIR__ . "/v1/Mobile/Profile.php";
include __DIR__ . "/v1/Mobile/Driver.php";


Route::get("/image", function (Request $request) {

    
    // continue to test optimize image 


});

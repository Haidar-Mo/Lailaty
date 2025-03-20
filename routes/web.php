<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('migrate', function () {

    Artisan::call('migrate');
    $title = 'Migration Done !!';
    return view('artisan-response', compact('title'));
});

Route::get('seed', function () {

    Artisan::call('db:seed');
    $title = 'Seed Done !!';
    return view('artisan-response', compact('title'));
});

Route::get('migrate-refresh', function () {

    Artisan::call('migrate:refresh');
    $title = 'Migration-Refresh Done !!';
    return view('artisan-response', compact('title'));
});


Route::get('storage-link',function (){
    Artisan::call('storage:link');
    $title = 'Storage Link Done !!';
    return view('artisan-response', compact('title'));
});
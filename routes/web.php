<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\ConnectExternalAuthenticationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::view('/', 'welcome');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('auth/login/{provider}', [ConnectExternalAuthenticationController::class, 'redirectToProvider'])
    ->where('provider', '(gitlab)')
    ->name('connect.provider');

Route::get('auth/callback/{provider}', [ConnectExternalAuthenticationController::class, 'handleProviderCallback'])
    ->where('provider', '(gitlab)')
    ->name('connect.callback');

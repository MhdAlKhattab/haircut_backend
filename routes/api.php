<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\CounterController;

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

// For Test
Route::get('/test-online', function () {
    // dd('Ok');
    return 1;
});

// Auth
Route::post('/add-manager',[AuthController::class,'AddManager']);
Route::post('/login',[AuthController::class,'Login']);


Route::group(['middleware' => 'auth:api'], function(){
    
    // Auth
    Route::post('/add-barber',[AuthController::class,'AddBarber']);
    Route::post('/logout',[AuthController::class,'Logout']);

    // User
    Route::get('/user',[UserController::class,'Me']);   
    Route::put('/user',[userController::class,'Update']);

});



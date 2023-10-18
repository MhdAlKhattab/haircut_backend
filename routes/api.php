<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GeneralServiceProviderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;

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
    dd('Ok');
});

// Auth
Route::post('/add-manager',[AuthController::class,'addManager']);
Route::post('/login',[AuthController::class,'Login']);


Route::group(['middleware' => 'auth:api'], function(){
    
    // Auth
    Route::post('/add-barber',[AuthController::class,'addBarber']);
    Route::post('/logout',[AuthController::class,'Logout']);

    // User
    Route::get('/user',[UserController::class,'Me']);   
    Route::put('/user',[userController::class,'updateUser']);

    // Branch
    Route::post('/branch',[BranchController::class,'addBranch']);
    Route::get('/branch',[BranchController::class,'getBranches']);
    Route::delete('/branch/{id}',[BranchController::class,'deleteBranch']);

    // Customer
    Route::post('/customer',[CustomerController::class,'addCustomer']);
    Route::get('/customer/{branch}',[CustomerController::class,'getCustomers']);
    Route::put('/customer/{id}',[CustomerController::class,'updateCustomer']);
    Route::delete('/customer/{id}',[CustomerController::class,'deleteCustomer']);

    // General Provider
    Route::post('/provider',[GeneralServiceProviderController::class,'addProvider']);
    Route::get('/provider/{branch}',[GeneralServiceProviderController::class,'getProviders']);
    Route::put('/provider/{id}',[GeneralServiceProviderController::class,'updateProvider']);
    Route::delete('/provider/{id}',[GeneralServiceProviderController::class,'deleteProvider']);

    // Product
    Route::post('/product',[ProductController::class,'addProduct']);
    Route::get('/product/{branch}',[ProductController::class,'getProducts']);
    Route::put('/product/{id}',[ProductController::class,'updateProduct']);
    Route::delete('/product/{id}',[ProductController::class,'deleteProduct']);

    // Service
    Route::post('/service',[ServiceController::class,'addService']);
    Route::get('/service/{branch}',[ServiceController::class,'getServices']);
    Route::put('/service/{id}',[ServiceController::class,'updateService']);
    Route::delete('/service/{id}',[ServiceController::class,'deleteService']);
    
});



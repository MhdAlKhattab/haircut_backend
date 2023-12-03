<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StopedReservationController;
use App\Http\Controllers\GeneralServiceProviderController;
use App\Http\Controllers\GeneralServiceTermController;
use App\Http\Controllers\GeneralServiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SundryProductController;
use App\Http\Controllers\SalonDateController;
use App\Http\Controllers\CashierDepositController;
use App\Http\Controllers\CashierWithdrawController;
use App\Http\Controllers\AdvancePayController;
use App\Http\Controllers\RivalController;
use App\Http\Controllers\PurchaseController;

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
    Route::post('/barber',[AuthController::class,'addBarber']);
    Route::put('/barber/{id}',[AuthController::class,'updateBarber']);
    Route::post('/logout',[AuthController::class,'Logout']);

    // User
    Route::get('/user',[UserController::class,'Me']);
    Route::get('/user/{branch}',[UserController::class,'getUsers']);
    Route::post('/user/{branch}',[UserController::class,'searchUsers']);
    Route::put('/user',[userController::class,'updateUser']);

    // Branch
    Route::post('/branch',[BranchController::class,'addBranch']);
    Route::get('/branch',[BranchController::class,'getBranches']);
    Route::delete('/branch/{id}',[BranchController::class,'deleteBranch']);

    // Customer
    Route::post('/customer',[CustomerController::class,'addCustomer']);
    Route::get('/customer/{branch}',[CustomerController::class,'getCustomers']);
    Route::post('/customer/{branch}',[CustomerController::class,'searchCustomers']);
    Route::put('/customer/{id}',[CustomerController::class,'updateCustomer']);
    Route::delete('/customer/{id}',[CustomerController::class,'deleteCustomer']);

    // Employee
    Route::post('/employee',[EmployeeController::class,'addEmployee']);
    Route::post('/pay-commission',[EmployeeController::class,'payCommission']);
    Route::get('/employee/{branch}',[EmployeeController::class,'getEmployees']);
    Route::post('/employee/{branch}',[EmployeeController::class,'searchEmployees']);
    Route::get('/employee-info/{branch}',[EmployeeController::class,'getEmployeesInfo']);
    Route::post('/employee-info/{branch}',[EmployeeController::class,'searchEmployeesInfo']);
    Route::put('/employee/{id}',[EmployeeController::class,'updateEmployee']);
    Route::delete('/employee/{id}',[EmployeeController::class,'deleteEmployee']);

    // Order
    Route::post('/order',[OrderController::class,'addOrder']);
    Route::get('/order/{branch}',[OrderController::class,'getOrders']);
    Route::post('/order/{branch}',[OrderController::class,'searchOrders']);
    Route::post('/filter-order/{branch}',[OrderController::class,'filterOrders']);
    Route::get('/daily-report/{branch}',[OrderController::class,'getDailyReport']);
    Route::post('/filter-daily-report/{branch}',[OrderController::class,'filterDailyReport']);
    Route::put('/order/{id}',[OrderController::class,'updateOrder']);
    Route::delete('/order/{id}',[OrderController::class,'deleteOrder']);

    // Purchase
    Route::post('/purchase',[PurchaseController::class,'addPurchase']);
    Route::get('/product-purchase/{branch}',[PurchaseController::class,'getProductPurchases']);
    Route::get('/sundry-purchase/{branch}',[PurchaseController::class,'getSundryPurchases']);
    Route::post('/product-purchase/{branch}',[PurchaseController::class,'searchProductPurchases']);
    Route::post('/sundry-purchase/{branch}',[PurchaseController::class,'searchSundryPurchases']);
    Route::post('/filter-product-purchase/{branch}',[PurchaseController::class,'filterProductPurchases']);
    Route::post('/filter-sundry-purchase/{branch}',[PurchaseController::class,'filterSundryPurchases']);
    Route::put('/purchase/{id}',[PurchaseController::class,'updatePurchase']);
    Route::delete('/purchase/{id}',[PurchaseController::class,'deletePurchase']);

    // Reservation
    Route::post('/reservation',[ReservationController::class,'addReservation']);
    Route::get('/reservation/{branch}',[ReservationController::class,'getReservations']);
    Route::put('/reservation/{id}',[ReservationController::class,'updateReservation']);
    Route::delete('/reservation/{id}',[ReservationController::class,'deleteReservation']);

    // Stoped Reservation
    Route::post('/stoped-reservation',[StopedReservationController::class,'addStopedReservation']);
    Route::get('/stoped-reservation/{branch}',[StopedReservationController::class,'getStopedReservations']);
    Route::put('/stoped-reservation/{id}',[StopedReservationController::class,'updateStopedReservation']);
    Route::delete('/stoped-reservation/{id}',[StopedReservationController::class,'deleteStopedReservation']);

    // General Provider
    Route::post('/provider',[GeneralServiceProviderController::class,'addProvider']);
    Route::get('/provider/{branch}',[GeneralServiceProviderController::class,'getProviders']);
    Route::post('/provider/{branch}',[GeneralServiceProviderController::class,'searchProviders']);
    Route::get('/untaxedprovider/{branch}',[GeneralServiceProviderController::class,'getUntaxedProviders']);
    Route::get('/taxedprovider/{branch}',[GeneralServiceProviderController::class,'gettaxedProviders']);
    Route::put('/provider/{id}',[GeneralServiceProviderController::class,'updateProvider']);
    Route::delete('/provider/{id}',[GeneralServiceProviderController::class,'deleteProvider']);

    // General Term
    Route::post('/term',[GeneralServiceTermController::class,'addTerm']);
    Route::get('/term/{branch}',[GeneralServiceTermController::class,'getTerms']);
    Route::post('/term/{branch}',[GeneralServiceTermController::class,'searchTerms']);
    Route::get('/untaxedterm/{branch}',[GeneralServiceTermController::class,'getUntaxedTerms']);
    Route::get('/taxedterm/{branch}',[GeneralServiceTermController::class,'gettaxedTerms']);
    Route::put('/term/{id}',[GeneralServiceTermController::class,'updateTerm']);
    Route::delete('/term/{id}',[GeneralServiceTermController::class,'deleteTerm']);

    // General Service
    Route::post('/general-service',[GeneralServiceController::class,'addService']);
    Route::get('/general-service/{branch}',[GeneralServiceController::class,'getServices']);
    Route::get('/general-untaxedservice/{branch}',[GeneralServiceController::class,'getUntaxedServices']);
    Route::get('/general-taxedservice/{branch}',[GeneralServiceController::class,'gettaxedServices']);
    Route::post('/general-untaxedservice/{branch}',[GeneralServiceController::class,'searchUntaxedServices']);
    Route::post('/general-taxedservice/{branch}',[GeneralServiceController::class,'searchtaxedServices']);
    Route::put('/general-service/{id}',[GeneralServiceController::class,'updateService']);
    Route::delete('/general-service/{id}',[GeneralServiceController::class,'deleteService']);

    // Product
    Route::post('/product',[ProductController::class,'addProduct']);
    Route::get('/product/{branch}',[ProductController::class,'getProducts']);
    Route::post('/product/{branch}',[ProductController::class,'searchProducts']);
    Route::put('/product/{id}',[ProductController::class,'updateProduct']);
    Route::delete('/product/{id}',[ProductController::class,'deleteProduct']);

    // Service
    Route::post('/service',[ServiceController::class,'addService']);
    Route::get('/service/{branch}',[ServiceController::class,'getServices']);
    Route::post('/service/{branch}',[ServiceController::class,'searchServices']);
    Route::get('/frequency-service/{branch}',[ServiceController::class,'getServicesFrequency']);
    Route::post('/frequency-service/{branch}',[ServiceController::class,'searchServicesFrequency']);
    Route::put('/service/{id}',[ServiceController::class,'updateService']);
    Route::delete('/service/{id}',[ServiceController::class,'deleteService']);

    // Supplier
    Route::post('/supplier',[SupplierController::class,'addSupplier']);
    Route::get('/supplier/{branch}',[SupplierController::class,'getSuppliers']);
    Route::post('/supplier/{branch}',[SupplierController::class,'searchSuppliers']);
    Route::put('/supplier/{id}',[SupplierController::class,'updateSupplier']);
    Route::delete('/supplier/{id}',[SupplierController::class,'deleteSupplier']);

    // Sundry Product
    Route::post('/sundry',[SundryProductController::class,'addSundryProduct']);
    Route::get('/sundry/{branch}',[SundryProductController::class,'getSundryProducts']);
    Route::post('/sundry/{branch}',[SundryProductController::class,'searchSundryProducts']);
    Route::put('/sundry/{id}',[SundryProductController::class,'updateSundryProduct']);
    Route::delete('/sundry/{id}',[SundryProductController::class,'deleteSundryProduct']);

    // Salon Date
    Route::post('/date',[SalonDateController::class,'addSalonDate']);
    Route::get('/date/{branch}',[SalonDateController::class,'getSalonDates']);
    Route::put('/date/{id}',[SalonDateController::class,'updateSalonDate']);
    Route::delete('/date/{id}',[SalonDateController::class,'deleteSalonDate']);

    // Cashier Deposit
    Route::post('/deposit',[CashierDepositController::class,'addCashierDeposit']);
    Route::get('/deposit/{branch}',[CashierDepositController::class,'getCashierDeposits']);
    Route::post('/filter-deposit/{branch}',[CashierDepositController::class,'filterCashierDeposits']);
    Route::put('/deposit/{id}',[CashierDepositController::class,'updateCashierDeposit']);
    Route::delete('/deposit/{id}',[CashierDepositController::class,'deleteCashierDeposit']);

    // Cashier Withdraw
    Route::post('/withdraw',[CashierWithdrawController::class,'addCashierWithdraw']);
    Route::get('/withdraw/{branch}',[CashierWithdrawController::class,'getCashierWithdraws']);
    Route::post('/filter-withdraw/{branch}',[CashierWithdrawController::class,'filterCashierWithdraws']);
    Route::put('/withdraw/{id}',[CashierWithdrawController::class,'updateCashierWithdraw']);
    Route::delete('/withdraw/{id}',[CashierWithdrawController::class,'deleteCashierWithdraw']);

    // Advance Pay
    Route::post('/advance',[AdvancePayController::class,'addAdvancePay']);
    Route::get('/advance/{branch}',[AdvancePayController::class,'getAdvancePays']);
    Route::post('/advance/{branch}',[AdvancePayController::class,'searchAdvancePays']);
    Route::post('/filter-advance/{branch}',[AdvancePayController::class,'filterAdvancePays']);
    Route::put('/advance/{id}',[AdvancePayController::class,'updateAdvancePay']);
    Route::delete('/advance/{id}',[AdvancePayController::class,'deleteAdvancePay']);

    // Rival
    Route::post('/rival',[RivalController::class,'addRival']);
    Route::get('/rival/{branch}',[RivalController::class,'getRivals']);
    Route::post('/rival/{branch}',[RivalController::class,'searchRivals']);
    Route::post('/filter-rival/{branch}',[RivalController::class,'filterRivals']);
    Route::put('/rival/{id}',[RivalController::class,'updateRival']);
    Route::delete('/rival/{id}',[RivalController::class,'deleteRival']);
    
});



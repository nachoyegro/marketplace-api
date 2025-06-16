<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

Route::get('billing-by-company', [CompanyController::class, 'billingByCompany'])
    ->middleware(['auth:sanctum', 'role:admin']);

Route::get('/consumption-last-week', [OrderController::class, 'consumptionLastWeek'])
    ->middleware(['auth:sanctum', 'role:admin']);

// Resource route for companies

Route::resource('companies', CompanyController::class);
// Resource route for Employees
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('employees', EmployeeController::class);
});
// Resource route for Benefits
Route::resource('benefits', BenefitController::class);
// Resource route for Variations
Route::resource('variations', VariationController::class);
// Resource route for Orders
Route::resource('orders', OrderController::class);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TurnController;
use App\Http\Controllers\FoodController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function (Request $request) {
    return 'Hola mundo';
});

Route::get('customers/byDni/{dni}', [CustomerController::class, 'byDni']);
Route::get('customers/byKey/{key}', [CustomerController::class, 'byKey']);
Route::post('customers/massive', [CustomerController::class, 'storeMassive']);

Route::get('businesses/all', [BusinessController::class, 'allBusinesses']);

Route::get('foods/byCustomerRangeDate/{customerId}/{sd}/{ed}', [FoodController::class, 'byCustomerRangeDate']);
Route::get('foods/byCustomerDate/{customerId}/{date}', [FoodController::class, 'byCustomerDate']);
Route::get('foods/byBusinessRangeDate/{businessId}/{sd}/{ed}', [FoodController::class, 'byBusinessRangeDate']);

Route::get('turns/byDate/{date}', [TurnController::class, 'byDate']);


Route::post('foods/withTurn', [FoodController::class, 'storeFood']);

Route::apiResources([
    'businesses' => BusinessController::class,
    'customers' => CustomerController::class,
    'turns' => TurnController::class,
    'foods' => FoodController::class,
]);
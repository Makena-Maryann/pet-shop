<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\PaymentController;
use App\Http\Controllers\v1\OrderStatusController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource([
    'v1/users', UserController::class,
    'v1/orders', OrderController::class,
    'v1/payments', PaymentController::class,
    'v1/order-statuses', OrderStatusController::class,
]);

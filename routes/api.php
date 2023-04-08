<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\PaymentController;
use App\Http\Controllers\Api\v1\OrderStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::controller(AdminController::class)->prefix('admin')->name('admin.')->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->name('logout');

        Route::middleware('auth.token')->group(function () {
            Route::post('create', 'store')->name('create');
            Route::get('user-listing', 'userListing')->name('user-listing');
            Route::put('user-edit/{uuid}', 'userEdit')->name('user-edit');
            Route::delete('user-delete/{uuid}', 'userDelete')->name('user-delete');
        });
    });

    Route::apiResources([
        'users' => UserController::class,
        'orders' => OrderController::class,
        'payments' => PaymentController::class,
        'order-statuses' => OrderStatusController::class,
    ]);
});

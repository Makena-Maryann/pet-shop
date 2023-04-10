<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\OrderController;
use App\Http\Controllers\Api\v1\PaymentController;
use App\Http\Controllers\Api\v1\OrderStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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
    Route::controller(AuthenticatedSessionController::class)->group(function () {
        Route::post('admin/login', 'store')->name('admin.login');
        Route::post('admin/logout', 'destroy')->name('admin.logout');
        Route::post('user/login', 'store')->name('user.login');
        Route::post('user/logout', 'destroy')->name('user.logout');
    });

    Route::middleware('auth.token:admin')->controller(AdminController::class)->prefix('admin')->name('admin.')->group(function () {
        Route::post('create', 'store')->name('create');
        Route::get('user-listing', 'userListing')->name('user-listing');
        Route::put('user-edit/{user}', 'userEdit')->name('user-edit');
        Route::delete('user-delete/{user}', 'userDelete')->name('user-delete');
    });

    Route::apiResources([
        'users' => UserController::class,
        'orders' => OrderController::class,
        'payments' => PaymentController::class,
        'order-statuses' => OrderStatusController::class,
    ]);
});

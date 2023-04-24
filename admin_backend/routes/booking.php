<?php

use App\Http\Controllers\API\v1\Dashboard\{Admin, Seller, User};
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'v1', 'middleware' => ['block.ip']], function () {

    Route::group(['prefix' => 'dashboard'], function () {

        // USER BLOCK
        Route::group(['prefix' => 'user', 'middleware' => ['sanctum.check'], 'as' => 'user.'], function () {

            /* User Bookings */
            Route::apiResource('bookings', User\Booking\UserBookingController::class);
            Route::delete('bookings/delete',        [User\Booking\UserBookingController::class, 'destroy']);

        });

        // SELLER BLOCK
        Route::group(['prefix' => 'seller', 'middleware' => ['sanctum.check', 'role:seller|moderator'], 'as' => 'seller.'], function () {

            /* Bookings */
            Route::apiResource('bookings', Seller\Booking\BookingController::class);
            Route::delete('bookings/delete',        [Seller\Booking\BookingController::class, 'destroy']);

            /* Shop Section */
            Route::apiResource('shop-sections', Seller\Booking\ShopSectionController::class);
            Route::delete('shop-sections/delete',        [Seller\Booking\ShopSectionController::class, 'destroy']);

            /* Tables */
            Route::apiResource('tables', Seller\Booking\TableController::class);
            Route::delete('tables/delete',        [Seller\Booking\TableController::class, 'destroy']);

        });

        // ADMIN BLOCK
        Route::group(['prefix' => 'admin', 'middleware' => ['sanctum.check', 'role:admin|manager'], 'as' => 'admin.'], function () {

            /* Bookings */
            Route::apiResource('bookings',      Admin\Booking\BookingController::class);
            Route::delete('bookings/delete',        [Admin\Booking\BookingController::class, 'destroy']);
            Route::get('bookings/drop/all',         [Admin\Booking\BookingController::class, 'dropAll']);
            Route::get('bookings/restore/all',      [Admin\Booking\BookingController::class, 'restoreAll']);
            Route::get('bookings/truncate/db',      [Admin\Booking\BookingController::class, 'truncate']);

            /* User Bookings */
            Route::apiResource('user-bookings', Admin\Booking\UserBookingController::class);
            Route::delete('user-bookings/delete',   [Admin\Booking\UserBookingController::class, 'destroy']);
            Route::get('user-bookings/drop/all',    [Admin\Booking\UserBookingController::class, 'dropAll']);
            Route::get('user-bookings/restore/all', [Admin\Booking\UserBookingController::class, 'restoreAll']);
            Route::get('user-bookings/truncate/db', [Admin\Booking\UserBookingController::class, 'truncate']);

            /* Shop Section */
            Route::apiResource('shop-sections', Admin\Booking\ShopSectionController::class);
            Route::delete('shop-sections/delete',   [Admin\Booking\ShopSectionController::class, 'destroy']);
            Route::get('shop-sections/drop/all',    [Admin\Booking\ShopSectionController::class, 'dropAll']);
            Route::get('shop-sections/restore/all', [Admin\Booking\ShopSectionController::class, 'restoreAll']);
            Route::get('shop-sections/truncate/db', [Admin\Booking\ShopSectionController::class, 'truncate']);

            /* Tables */
            Route::apiResource('tables',        Admin\Booking\TableController::class);
            Route::delete('tables/delete',          [Admin\Booking\TableController::class, 'destroy']);
            Route::get('tables/drop/all',           [Admin\Booking\TableController::class, 'dropAll']);
            Route::get('tables/restore/all',        [Admin\Booking\TableController::class, 'restoreAll']);
            Route::get('tables/truncate/db',        [Admin\Booking\TableController::class, 'truncate']);

        });

    });

});

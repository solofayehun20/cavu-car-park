<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

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

Route::prefix('api')->group(function () {
    Route::get('/check-availability', [BookingController::class, 'checkAvailability']);
    Route::get('/check-price', [BookingController::class, 'checkPrice']);
    Route::post('/create-booking', [BookingController::class, 'createBooking']);
    Route::delete('/cancel-booking/{id}', [BookingController::class, 'cancelBooking']);
    Route::put('/amend-booking/{id}', [BookingController::class, 'amendBooking']);
});

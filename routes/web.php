<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {return view('welcome');});
Route::get('/check-availability', [BookingController::class, 'checkAvailability']);
Route::get('/check-price', [BookingController::class, 'checkPrice']);
Route::post('/create-booking', [BookingController::class, 'createBooking']);
Route::post('/cancel-booking/{bookingId}', [BookingController::class, 'cancelBooking']);
Route::post('/amend-booking/{bookingId}', [BookingController::class, 'amendBooking']);


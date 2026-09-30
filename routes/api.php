<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

Route::post('new-guest', [GuestController::class, 'new_guest']);
Route::get('all-guests', [GuestController::class, 'get_all_guests']);
Route::get('guest-book/search/{name}', [GuestController::class, 'getByName']); // for autofill

// Route::post('guestbook', [GuestBookController::class, 'store']);
// Route::get('guest-sijelapp', [GuestBookController::class, 'getAllGuests_Sijelapp']);

// FOR CHART DASHBOARD
Route::get('guests', [GuestController::class, 'get_guests']);

// Rute API untuk Frontend E-Tamu (Next.js)
Route::get('e-tamu/init', [\App\Http\Controllers\PresensiController::class, 'initDataApi']);
Route::post('e-tamu/store', [\App\Http\Controllers\PresensiController::class, 'storeApi']);
Route::post('e-tamu/survey', [\App\Http\Controllers\PresensiController::class, 'storeSurveyApi']);

// Tambahkan rute ini untuk Dashboard
Route::get('e-tamu/dashboard', [\App\Http\Controllers\DashboardController::class, 'getDashboardApi']);

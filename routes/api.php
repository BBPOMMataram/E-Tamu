<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

Route::post('new-guest', [GuestController::class, 'new_guest']);
Route::get('all-guests', [GuestController::class, 'get_all_guests']);
Route::get('guest-book/search/{name}', [GuestController::class, 'getByName']); // for autofill

// Route::post('guestbook', [GuestBookController::class, 'store']);
// Route::get('guest-sijelapp', [GuestBookController::class, 'getAllGuests_Sijelapp']);

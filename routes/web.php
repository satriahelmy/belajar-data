<?php

use App\Http\Controllers\FoundationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('foundation'))->name('home');
Route::get('/__foundation', FoundationController::class)->name('foundation');

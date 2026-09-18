<?php

use App\Http\Controllers\FoundationController;
use App\Http\Controllers\SpikeLessonController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('foundation'))->name('home');
Route::get('/__foundation', FoundationController::class)->name('foundation');
Route::get('/__spike/lesson', SpikeLessonController::class)->name('spike.lesson');

<?php

use App\Http\Controllers\FoundationController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SqlSpikeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('foundation'))->name('home');
Route::get('/__foundation', FoundationController::class)->name('foundation');
Route::get('/__spike/sql', SqlSpikeController::class)->name('spike.sql');
Route::get('/learn/{pathKey}/{moduleKey}/{topicKey}', LessonController::class)
    ->where([
        'pathKey' => '[a-z0-9][a-z0-9-]*',
        'moduleKey' => '[a-z0-9][a-z0-9-]*',
        'topicKey' => '[a-z0-9][a-z0-9-]*',
    ])
    ->name('learning.lesson');
Route::get('/downloads/nusamart-module-04-fallback.ipynb', function () {
    return response()->download(
        public_path('downloads/nusamart-module-04-fallback.ipynb'),
        'nusamart-module-04-fallback.ipynb',
        ['Content-Type' => 'application/json'],
    );
})->name('downloads.nusamart-module-04-fallback');

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\FoundationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\SqlSpikeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->prefix('progress')->group(function () {
    Route::get('/', [ProgressController::class, 'page'])->name('progress.index');
    Route::get('/topics', [ProgressController::class, 'index'])->name('progress.topics');
    Route::post('/topics/start', [ProgressController::class, 'start'])->name('progress.topics.start');
    Route::post('/topics/complete', [ProgressController::class, 'complete'])->name('progress.topics.complete');
    Route::post('/merge-guest', [ProgressController::class, 'mergeGuest'])->name('progress.merge-guest');
});
Route::middleware('auth')->prefix('bookmarks')->group(function () {
    Route::get('/', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
});
Route::get('/learn', LearnController::class)->name('learning.index');
Route::get('/learn/{moduleKey}', ModuleController::class)
    ->where('moduleKey', '[a-z0-9][a-z0-9-]*')
    ->name('learning.module');
Route::get('/learn/{pathKey}/{moduleKey}/challenge', ChallengeController::class)
    ->where([
        'pathKey' => '[a-z0-9][a-z0-9-]*',
        'moduleKey' => '[a-z0-9][a-z0-9-]*',
    ])
    ->name('learning.challenge');
Route::get('/__foundation', FoundationController::class)->name('foundation');
Route::get('/__spike/sql', SqlSpikeController::class)->name('spike.sql');
Route::get('/learn/{pathKey}/{moduleKey}/{topicKey}', LessonController::class)
    ->where([
        'pathKey' => '[a-z0-9][a-z0-9-]*',
        'moduleKey' => '[a-z0-9][a-z0-9-]*',
        'topicKey' => '[a-z0-9][a-z0-9-]*',
    ])
    ->name('learning.lesson');
Route::get('/skills', SkillsController::class)->name('skills.index');
Route::get('/projects', ProjectsController::class)->name('projects.index');
Route::get('/downloads/nusamart-module-04-fallback.ipynb', function () {
    return response()->download(
        public_path('downloads/nusamart-module-04-fallback.ipynb'),
        'nusamart-module-04-fallback.ipynb',
        ['Content-Type' => 'application/json'],
    );
})->name('downloads.nusamart-module-04-fallback');

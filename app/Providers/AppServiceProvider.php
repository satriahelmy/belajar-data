<?php

namespace App\Providers;

use App\Domain\Learner\Assessment\AttemptStore;
use App\Domain\Learner\Assessment\DatabaseAttemptStore;
use App\Domain\Learner\Progress\DatabaseProgressStore;
use App\Domain\Learner\Progress\ProgressStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProgressStore::class, DatabaseProgressStore::class);
        $this->app->bind(AttemptStore::class, DatabaseAttemptStore::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

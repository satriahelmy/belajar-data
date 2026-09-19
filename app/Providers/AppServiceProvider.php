<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\JournalBook\JournalBookRepositoryInterface;
use App\Repositories\JournalBook\JournalBookRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // JournalBook
        $this->app->bind(
            JournalBookRepositoryInterface::class,
            JournalBookRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Record\RecordRepositoryInterface;
use App\Repositories\Record\RecordRepository;
use App\Repositories\JournalRegister\JournalRegisterRepositoryInterface;
use App\Repositories\JournalRegister\JournalRegisterRepository;

use App\Repositories\AccountSubjects\AccountSubjectsRepositoryInterface;
use App\Repositories\AccountSubjects\AccountSubjectsRepository;

use App\Repositories\Bank\BankRepositoryInterface;
use App\Repositories\Bank\BankRepository;

use App\Repositories\Supplier\SupplierRepositoryInterface;
use App\Repositories\Supplier\SupplierRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Record
        $this->app->bind(
            RecordRepositoryInterface::class,
            RecordRepository::class,
        );
        // JournalRegister
        $this->app->bind(
            JournalRegisterRepositoryInterface::class,
            JournalRegisterRepository::class,
        );
        // AccountSubjectsRepository
        $this->app->bind(
            AccountSubjectsRepositoryInterface::class,
            AccountSubjectsRepository::class,
        );
        // Bank
        $this->app->bind(
            BankRepositoryInterface::class,
            BankRepository::class,
        );
        // Supplier
        $this->app->bind(
            SupplierRepositoryInterface::class,
            SupplierRepository::class,
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

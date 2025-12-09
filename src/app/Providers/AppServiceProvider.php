<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ExpenseTypeRepositoryInterface;
use App\Interfaces\PaymentMethodRepositoryInterface;
use App\Interfaces\ExpenseRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\ExpenseTypeRepository;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\ExpenseRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
    
        $this->app->bind(
            ExpenseTypeRepositoryInterface::class,
            ExpenseTypeRepository::class
        );
        $this->app->bind(
            PaymentMethodRepositoryInterface::class,
            PaymentMethodRepository::class
        );
        $this->app->bind(
            ExpenseRepositoryInterface::class,
            ExpenseRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

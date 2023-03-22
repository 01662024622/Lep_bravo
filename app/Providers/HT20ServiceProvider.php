<?php

namespace App\Providers;

use App\Services\HT00\CategoryService;
use App\Services\HT20\ApartmentService;
use App\Services\HT20\GroupService;
use App\Services\HT20\UserService;
use App\Services\Impl\HT00\CategoryServiceImpl;
use App\Services\Impl\HT20\ApartmentServiceImpl;
use App\Services\Impl\HT20\GroupServiceImpl;
use App\Services\Impl\HT20\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class HT20ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            ApartmentService::class,
            ApartmentServiceImpl::class
        );
        $this->app->singleton(
            UserService::class,
            UserServiceImpl::class
        );
        $this->app->singleton(
            GroupService::class,
            GroupServiceImpl::class
        );

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

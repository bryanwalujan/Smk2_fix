<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HolidayService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(HolidayService::class, function ($app) {
            return new HolidayService();
        });
    }

    public function boot()
    {
        //
    }
}
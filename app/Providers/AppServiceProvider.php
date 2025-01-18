<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NewsApiOrgService;
use App\Services\NewsCredService;
use App\Services\NewsServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NewsServiceInterface::class, NewsApiOrgService::class);
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

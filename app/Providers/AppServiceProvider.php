<?php

namespace App\Providers;

use App\Service\ReqRes\ReqResApi;
use App\Service\ReqRes\ReqResApiInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        ReqResApiInterface::class => ReqResApi::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
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

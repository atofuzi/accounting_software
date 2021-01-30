<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomValidator;

class CustomValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->resolver(function($translator,$data,$rules,$messages,$attributes){
            return new CustomValidator($translator,$data,$rules,$messages,$attributes);
        });
    }
}

<?php

namespace App\Providers;

use App\Product_images;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        ini_set('memory_limit','200M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

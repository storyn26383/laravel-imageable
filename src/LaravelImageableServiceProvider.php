<?php

namespace Sasaya\LaravelImageable;

use Sasaya\LaravelImageable\Image;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelImageableServiceProvider extends ServiceProvider
{
    /**
     * Boot the services for the application
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Route::get('images/{image}', function (Image $image) {
            return $image->response();
        })->name('images.show')->middleware('web');
    }
}

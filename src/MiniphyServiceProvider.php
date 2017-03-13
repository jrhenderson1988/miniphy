<?php

namespace Miniphy;

use Illuminate\Support\ServiceProvider;

class MiniphyServiceProvider extends ServiceProvider
{
    /**
     * Register the compiler and minifiers.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('miniphy', function() {
            return new Miniphy();
        });
    }
}

<?php

namespace Miniphy;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

class MiniphyServiceProvider extends ServiceProvider
{
    /**
     * Register the Miniphy instance and optionally register the compiler.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('miniphy', function() {
            return new Miniphy();
        });

        $this->registerCompiler();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $app = $this->app;

        $app->view->getEngineResolver()->register('blade', function () use($app) {
            $compiler = $app['miniphy.compiler'];

            return new CompilerEngine($compiler);
        });

        $app->view->addExtension('blade.php', 'blade');
    }

    protected function registerCompiler()
    {
        $this->app->singleton('miniphy.compiler', function ($app) {
            $minifier = $app['miniphy'];
            $files = $app['files'];
            $storage = $app->config->get('view.compiled');

            return new MiniphyCompiler($minifier, $files, $storage);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['miniphy', 'miniphy.compiler'];
    }
}

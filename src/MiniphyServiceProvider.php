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
        $this->registerConfig();
        $this->registerMiniphy();
        $this->registerCompiler();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if (config('miniphy.blade')) {
            $this->overrideBladeCompiler();
        }
    }

    /**
     * Make Laravel aware of the publishable config file and merge the user defined settings with the defaults from it.
     */
    protected function registerConfig()
    {
        $config = realpath(__DIR__) . '/../config/miniphy.php';

        $this->publishes([$config => config_path('miniphy.php')]);

        $this->mergeConfigFrom($config, 'miniphy');
    }

    /**
     * Register the Miniphy instance with the application's IoC container.
     */
    protected function registerMiniphy()
    {
        $this->app->singleton('miniphy', function() {
            $miniphy = new Miniphy();

            $miniphy->setDefaultHtmlDriverKey(config('miniphy.html.driver', 'regex'));
            $miniphy->setDefaultCssDriverKey(config('miniphy.css.driver', 'regex'));

            // TODO - Set the default HTML mode from the config.

            return $miniphy;
        });
    }

    /**
     * Register the MiniphyCompiler instance that we may use to override the default BladeCompiler if the blade override
     * is enabled in the config.
     */
    protected function registerCompiler()
    {
        $this->app->singleton('miniphy.compiler', function ($app) {
            return new MiniphyCompiler($app['miniphy'], $app['files'], $app->config->get('view.compiled'));
        });
    }

    protected function overrideBladeCompiler()
    {
        $app = $this->app;

        $app->view->getEngineResolver()->register('blade', function () use($app) {
            $compiler = $app['miniphy.compiler'];

            return new CompilerEngine($compiler);
        });

        $app->view->addExtension('blade.php', 'blade');
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

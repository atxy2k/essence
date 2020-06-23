<?php

namespace Atxy2k\Essence;

use Atxy2k\Essence\Commands\EssenceInstall;
use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class EssenceServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'atxy2k');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'atxy2k');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/essence.php', 'essence');

        // Register the service the package provides.
        $this->app->singleton('essence', function (Container $app) {
            return $app->make(Essence::class);
        });

//        $this->app->register(SentinelServiceProvider::class);
//
//        $alias = AliasLoader::getInstance();
//        $alias->alias('Activation', Activation::class);
//        $alias->alias('Reminder', Reminder::class);
//        $alias->alias('Sentinel', Sentinel::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['essence','auth'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        $this->commands([
            EssenceInstall::class
        ]);
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/essence.php' => config_path('essence.php'),
        ], 'essence.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/atxy2k'),
        ], 'essence.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/atxy2k'),
        ], 'essence.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/atxy2k'),
        ], 'essence.views');*/
    }
}

<?php

namespace Atxy2k\Essence;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Prologue\Alerts\AlertsServiceProvider;
use Prologue\Alerts\Facades\Alert;

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
        /**********************************************
         * Adding laravel collective
         **********************************************/
        $this->app->register(HtmlServiceProvider::class);
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', FormFacade::class);
        $loader->alias('HTML', HtmlFacade::class);
        /**********************************************
         * Adding alerts
         **********************************************/
        $this->app->register(AlertsServiceProvider::class);
        $loader->alias('Alert', Alert::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['essence'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
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

        // Registering package commands.
        // $this->commands([]);
    }
}

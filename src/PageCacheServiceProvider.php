<?php
namespace Jsefton\PageCache;

use Illuminate\Support\ServiceProvider;
use JSefton\PageCache\Commands\ClearPageCache;

class PageCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Configuration
        $this->publishes([
            __DIR__.'/../config/' => config_path(),
        ], 'pagecache.config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.pagecache.clear', function ($app) {
            return new ClearPageCache();
        });

        // Register commands
        $this->commands([
            'command.pagecache.clear'
        ]);
    }
}

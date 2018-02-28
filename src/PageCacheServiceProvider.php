<?php
namespace Jsefton\PageCache;

use Illuminate\Support\ServiceProvider;
use JSefton\PageCache\Commands\ClearPageCache;
use JSefton\PageCache\CacheObserver;

class PageCacheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($models = config('pagecache.models')) {
            foreach ($models as $model) {
                $model::observe(CacheObserver::class);
            }
        }

        // Configuration
        $this->publishes([
            __DIR__.'/../config/' => config_path(),
        ], 'pagecache.config');

        // Create temp folder needed
        $this->publishes([
            __DIR__.'/../storage' => storage_path('page_cache'),
        ], 'pagecache.storage');


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Load Caching Middleware
        if(config('pagecache.enabled')) {
            $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
            $kernel->pushMiddleware('JSefton\PageCache\Http\Middleware\PageCache');
        }

        $this->app->singleton('command.pagecache.clear', function ($app) {
            return new ClearPageCache();
        });

        // Register commands
        $this->commands([
            'command.pagecache.clear'
        ]);
    }
}

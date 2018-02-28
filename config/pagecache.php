<?php
return [
    /**
     * Enable the page cache package
     */
    'enabled'       => env('PAGE_CACHE_ENABLED', true),

    /**
     * Minify the cache response HTML
     */
    'minify'        => env('PAGE_CACHE_MINIFY', false),

    /**
     * Paths to exclude from being wrapped in cache
     *
     * E.g. admin to exclude /admin route
     */
    'exclude'       => [
        'admin'
    ],

    /**
     * Models that should be observed and cache cleared upon data change
     *
     * E.g. \App\Model::class
     */
    'models'     => []
];

<?php
return [
    'enabled'       => 1,
    'minify'        => 1,

    /**
     * Paths to exclude from being wrapped in cache
     */
    'exclude'       => [
        'admin'
    ],

    'paths'         => [],

    /**
     * Models that should be observed and cache cleared upon data change
     */
    'observers'     => []
];

<?php

namespace JSefton\PageCache;

class CacheObserver
{
    public function saved($model)
    {
        $cache = new HTMLCache(null, null);
        $cache->clearCache();
    }
}

<?php

namespace JSefton\PageCache\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use JSefton\PageCache\HTMLCache;

class PageCache
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only use caching templates when not logged in
        if (!app(Authenticatable::class)) {
            $cacher = new HTMLCache($request, $next, $request->path(),$request->getQueryString());
            $cacher->getContent();
        }
        return $next($request);
    }
}

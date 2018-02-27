<?php

namespace JSefton\PageCache\Http\Middleware;

use Auth;
use Closure;
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
        // Only use caching templates when not logged in and request is GET method
        if (!Auth::check() && $request->method() == 'GET') {
            $cacher = new HTMLCache($request, $next, $request->path(),$request->getQueryString());
            $cacher->getContent();
        }
        return $next($request);
    }
}

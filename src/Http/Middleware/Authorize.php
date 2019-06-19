<?php

namespace Just\Warehouse\Nova\Scan\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Just\Warehouse\Nova\Scan\Tool;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        return app(Tool::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }
}

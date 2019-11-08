<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_if(Gate::denies('check-admin'), 403, 'Permission denied.');

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class CheckForMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if ($this->app->isDownForMaintenance() &&
            !in_array($request->getClientIp(), ['47.107.190.67']))
        {
            return response('Be right back!', 503);
        }
    
        return $next($request);
    }
}

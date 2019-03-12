<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
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
        $error = (object) null;
        $error->status = 401;
        $error->message = 'unauthorized';
        $error->error = 'insufficient permissions';

        if($request->auth == null){
            return response()->json(compact('error'), 401);
        }

        $routes = $request->route();
        $actions = isset($routes[1]) ? $routes[1] : null;
        $roles = $actions != null && isset($actions['roles']) ? $actions['roles'] : null;

        if($request->auth->hasAnyRole($roles) || !$roles){
            return $next($request);
        }

        return response()->json(compact('error'), 401);
    }
}

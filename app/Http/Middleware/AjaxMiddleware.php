<?php

namespace App\Http\Middleware;

use Closure;

class AjaxMiddleware
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
        //$user = Sentinel::check();
        //dd($user->id);
        //dd($request->fullUrl());
        if($request->ajax() || $request->wantsJson()){
            return $next($request);
        }else{
            return response('Method Not Allowed.', 405);
        }
    }
}

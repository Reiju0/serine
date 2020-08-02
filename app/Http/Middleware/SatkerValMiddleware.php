<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use PublicFunction;

class SatkerValMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $kdsatker = false)
    {
        if($kdsatker == false){
            $kdsatker = $request->route('kdsatker');
        }
        
        $satker     = "SELECT a.*, b.kdkanwil from t_satker_blu a 
                        LEFT JOIN t_kppn b on a.kdkppn = b.kdkppn
                        WHERE a.kdsatker = ?";

        $query = DB::table(DB::raw("($satker) a"))
                ->setBindings([$kdsatker]);

        $return = false;

        $query = PublicFunction::WhereGroup($request->group, $query);
        if($query->count() > 0){
            $return = true;
        }

        if($return){
            return $next($request);
        }else{
            return response('Forbidden.', 403);
        }
    }
}

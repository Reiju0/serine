<?php

namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $permission = array_except(func_get_args(), [0,1]);
        $admin = array('admin', 'superadmin');
        //array_push($role, $admin);
        if (Sentinel::hasAnyAccess($admin)){
            $request->merge(array("group" => "all"));
            return $next($request);
        }elseif(Sentinel::hasAnyAccess($permission)){
            $roles = Sentinel::getUser()->getRoles();
            $user_groups = array();
            foreach ($roles as $role) {
                foreach ($permission as $perm) {
                    # code...
                    if(array_key_exists($perm, $role->permissions)){
                        if($role->getOriginal('pivot_user_groups') == '0' or empty($role->getOriginal('pivot_user_groups'))){
                            $user_groups = 'all';
                            break; 
                        }else{
                            if($user_groups != 'all'){
                                array_push($user_groups, $role->getOriginal('pivot_user_groups')); 
                            }                           
                        }

                        break;
                    }
                }
            }

            $request->merge(array("group" => $user_groups));
            return $next($request);
        }else{
            return response('Access Denied.', 403);
        }
    }
}

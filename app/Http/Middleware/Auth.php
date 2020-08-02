<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Closure;
use Cookie;
use Carbon\Carbon;
use DB;

class Auth
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

        if(isset($_COOKIE[config('session.cookie').'_temp'])){  
            $key = json_decode(Cookie::get(config('session.cookie').'_temp'));
            $exp = strtotime("now");
            //dd("$key->expires <br> $exp");

            if(!isset($key->token)){
                $key->token = 'a';
            }

            $data = DB::table('user_login')
                    ->where('user_id', $key->user)
                    ->where('status', '1')
                    ->where('token', $key->token)
                    ->first();

            //dd(strtotime($data->timeout));
            //dd("$key->expires <br> $exp");
            if ( 
                (!isset($key->expires))
                or ($exp > $key->expires) 
                or (!isset($key->user))
                or (!isset($key->login))
                or (!isset($data->timeout))
                or (strtotime($data->timeout) < $exp)
            ){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Unauthorized.', 401);
                } else {
                    return redirect()->guest('logout');
                }
            }else{
                $user = Sentinel::check();
                if($user){
                    if($user->id != $key->user){
                        $user = Sentinel::findById($key->user);
                        Sentinel::authenticate($user);
                    }
                }else{
                    $user = Sentinel::findById($key->user);
                    Sentinel::authenticate($user);
                }

                $users['id'] = $user->id;
                $users['aktif'] = $user->aktif;
                $users['nama'] = $user->nama;
                $users['nip'] = $user->nip;
                $users['foto'] = $user->foto;

                $request->merge(array("user" => (object) $users));
                
            }
        }else{
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('logout');
            }
        }

        //dd(config('session.lifetime'));

        $time = strtotime("+".config('session.lifetime')." minute");
        $data = json_encode(['user' => $key->user, 'tahun' => $key->tahun, 'login' => true, 'token' => $key->token, 'expires' => $time]);
        $request->merge(array("th" => $key->tahun));
        Cookie::queue(Cookie::make(config('session.cookie').'_temp', $data));

        $method = $request->method();
        $data = $request;

        if($method == 'POST'){
            $log['path'] = $request->url();
            $log['post'] = json_encode($data->except(['_token', 'old_password', 'new_password', 're_new_password', 'user']));
            if(strlen($log['post']) > 3000){
                $log['post'] = 'data is too long';
            }
            DB::table('logs')->insert([
                'user_id'   => $user->id,
                'year'      => $key->tahun,
                'logs'      => json_encode($log),
                'created_at'=> Carbon::now()
            ]);
            
            //dd($log);
        }

        return $next($request);
    }
}

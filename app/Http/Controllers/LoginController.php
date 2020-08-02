<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Http\Requests;
use DB;
use Session;
use Cache;
use Auth;
use Cookie;

class LoginController extends Controller
{
    //
    public function LogIn(Request $request)
	{
		$credentials = [
		    'login'    => PublicFunction::htmlTag($request->input('email')),
		    'password' => $request->input('password'),
		];

		// $tahun = PublicFunction::htmlTag($request->input('tahun'));
		// $allowed = array('2019');
		// if(!(in_array($tahun, $allowed))){
		// 	return view('login/login-lama', ['message' => 'Tahun salah', 'email' => $request->input('email')]);
		// }
		$tahun = '2019';

		$user = Sentinel::authenticate($credentials);
		
		if($user == false){
			return view('login/login-lama', ['message' => 'Username / Password salah', 'email' => $request->input('email')]);
		}

		$user = Sentinel::findByCredentials($credentials);
		if($user->aktif != 1){
			return view('login/login-lama', ['message' => 'User Tidak Aktif', 'email' => $request->input('email')]);
		}

		$token = $this->random_string();
		$time = strtotime("+".config('session.lifetime')." minute");

		$data = json_encode(['user' => $user->id, 'tahun' => $tahun, 'login' => true, 'token' => $token, 'expires' => $time]);
		Cookie::queue(Cookie::make(config('session.cookie').'_temp', $data));

		DB::table('user_login')
				->insert([
					'user_id' 	=> $user->id, 
					'token'		=> $token,
					'timeout' 	=> date('Y-m-d H:i:s', $time)
				]);

		return redirect()->guest('home');
		
	}

	public function LogInUser($user_id, $tahun){
		$time = time();
		$user = Sentinel::findById($user_id);
		$logout = strtotime($user->logout);
		
		Sentinel::login($user);
		Auth::loginUsingId($user->id);

		$array = (object) array('in'=>$user_id, 'out'=>$logout);
		//Cookie::queue(config('session.cookie').'_temp', $array, 15);
	}

	public function LogInUserCheck($user_id, $tahun){
		$time = time();
		$user = Sentinel::findById($user_id);
		$logout = strtotime($user->logout);

		if($time > $logout){
            return false;
        }

		if ((Sentinel::check() == false) or (Auth::check() == false))
		{
			Auth::loginUsingId($user->id);
			Sentinel::login($user);
		}else{
			$Sentinel = Sentinel::check();
			$auth = Auth::user();
			if($Sentinel->id != $auth->id){
				Sentinel::login($user);
			}
		}


        return true;
	}

	public function LogOutCheck($user_id, $tahun){
		$time = time();
		$user = Sentinel::findById($user_id);
		$logout = strtotime($user->logout);

		if ((Sentinel::check() == false) or (Auth::check() == false))
		{
			Auth::loginUsingId($user->id);
			Sentinel::login($user);
		}else{
			$Sentinel = Sentinel::check();
			$auth = Auth::user();
			if($Sentinel->id != $auth->id){
				Sentinel::login($user);
			}
		}

	}

	public function LogInCheck()
	{

		if(isset($_COOKIE[config('session.cookie').'_temp'])){  
            $key = json_decode(Cookie::get(config('session.cookie').'_temp'));
            $exp = strtotime("now");
            //dd($key);

            if(!isset($key->token)){
            	//$key->token = 'a';
            	return view('login/login');
            }

            $data = DB::table('user_login')
                    ->where('user_id', $key->user)
                    ->where('status', '1')
                    ->where('token', $key->token)
                    ->first();

            if (
                (isset($key->expires) && ($exp > $key->expires)) 
                or (!isset($key->user))
                or (!isset($key->login))
                or (!isset($data->timeout))
                or (strtotime($data->timeout) < $exp)
            ){
                
            }else{
                return redirect()->guest('home');
            }
        }

        return view('login/login-lama');
        
	}

	public function LogInCheckOut()
	{
		if ($user = Sentinel::check())
		{
			return redirect()->guest('home');
		}else{
			return redirect()->guest('login');
		}
	}

	public function SignUp()
	{
		echo "there's nothing in here";
	}

	public function LogOut()
	{                
		if(isset($_COOKIE[config('session.cookie').'_temp'])){  
            $key = json_decode(Cookie::get(config('session.cookie').'_temp'));

            //dd($key);

            if(isset($key->token)){
            	DB::table('user_login')
		            ->where('user_id', $key->user)
		            ->where('token', $key->token)
		            ->delete();
            }
			
	    }

		//dd("ok");
		Cookie::queue(Cookie::forget(config('session.cookie').'_temp'));
		Sentinel::logout();
		return redirect()->guest('login')->withErrors(['Anda Telah Keluar']);
	}

	public function random_string()
	{
		$size = 32;
	    $characters = array_merge(
	        range(0, 9),
	        range('A', 'Z')
	    );

	    $string = '';
	    $max = count($characters) - 1;
	    for ($i = 0; $i < $size; $i++) {
	        $string .= $characters[random_int(0, $max)];
	    }

	    return $string;

	}
}

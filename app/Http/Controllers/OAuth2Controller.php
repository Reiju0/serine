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

class OAuth2Controller extends LoginController
{
    
    public function index(Request $request)
	{
		
		if(isset($_COOKIE[config('session.cookie').'_temp'])){  
            $key = json_decode(Cookie::get(config('session.cookie').'_temp'));
            $exp = strtotime("now");
            

            if(isset($key->token)){
            	//$key->token = 'a';
            	$logd = DB::table('user_login')
                    ->where('user_id', $key->user)
                    ->where('status', '1')
                    ->where('token', $key->token)
                    ->first();

	            if (
	                (isset($key->expires) && ($exp > $key->expires)) 
	                or (!isset($key->user))
	                or (!isset($key->login))
	                or (!isset($logd->timeout))
	                or (strtotime($logd->timeout) < $exp)
	            ){
	                
	            }else{
	                return redirect()->guest('home');
	            }
            }   
        }

        //dd('login');
        

		$error = '';
		if(isset($_GET['error'])){
			$error = $_GET['error'];
		}
		
		if($error=='unsupported_response_type'){
			$data['error'] = 'Proses OAuth2 gagal!';
		}
		elseif($error=='invalid_auth_code1'){
			$data['error'] = 'Proses otorisasi gagal! (code : 1)';
		}
		elseif($error=='invalid_auth_code2'){
			$data['error'] = 'Proses otorisasi gagal! (code : 2)';
		}
		elseif($error=='invalid_user_profile1'){
			$data['error'] = 'Proses sinkronisasi user gagal! (code : 1)';
		}
		elseif($error=='invalid_user_profile2'){
			$data['error'] = 'Proses sinkronisasi user gagal! (code : 2)';
		}
		elseif($error=='inactive_user'){
			$data['error'] = 'User belum diaktifkan oleh Administrator!';
		}
		elseif($error=='invalid_state'){
			$data['error'] = 'Kode state tidak valid!';
		}
		
		$redirect_uri = url('/oauth2callback');
		$status = \config('oauth2.status');
		$url_portal = \config('oauth2.server');
		$client_id = \config('oauth2.client_id');
		$client_secret = \config('oauth2.client_secret');
		
		$now = new \DateTime();
		$state = $now->format('YmdHis');
		
		setcookie("state", $state, time()+3600);
		
		$url = $url_portal.'?response_type=code&client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&state='.$state;
		$login = '<a href="'.$url.'" class="btn btn-danger btn-raised btn-block">Login DIGIT</a>';
		
		$data['login'] = $login;
		
		if($status==true){
			return view('login/login-baru', $data);
		}
		else{
			return $this->LogInCheck($request);
		}
	}
	
	public function callback(Request $request)
	{
		if(null!==$request->input('code') && null!==$request->input('state')){//jika proses code authorization berhasil
			
			if(isset($_COOKIE['state'])){
				
				if($request->input('state')==$_COOKIE['state']){//jika state sama seperti sebelum diredirect ke portal
					
					$redirect_uri = url('/oauth2callback');
					$url_portal = \config('oauth2.server');
					$client_id = \config('oauth2.client_id');
					$client_secret = \config('oauth2.client_secret');
					
					$json = '{
						"code":"'.$_GET['code'].'",
						"grant_type":"code",
						"redirect_uri":"'.$redirect_uri.'",
						"client_id":"'.$client_id.'",
						"client_secret":"'.$client_secret.'"
					}';

					$handle = curl_init($url_portal.'/api/access-token');
					curl_setopt($handle, CURLOPT_POST, true);
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($handle, CURLOPT_POSTFIELDS, $json);
					curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$resp = curl_exec($handle);
					
					curl_close($handle);
					
					if(json_decode($resp)){
						
						$json = (array)json_decode($resp);
						$error = $json['error'];
						$code = $json['code'];
						$message = $json['message'];
						
						if($code=='00'){
							
							$id_user = $json['user_id'];
							$tahun = $json['tahun'];
							$access_token = $json['access_token'];
							
							$handle = curl_init($url_portal.'/api/user/'.$id_user);
							curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$access_token, 'Content-Type: application/json'));
							$resp = curl_exec($handle);
							
							curl_close($handle);
							
							if(json_decode($resp)){
								
								$data = (array)json_decode($resp);
								
								if($data['error']==false){
									
									$data = (array)$data['data'];
									
									$credentials = [
										'login' => $data['username']
									];
									
									$user = Sentinel::findByCredentials($credentials);
									
									if(isset($user->aktif)){
										
										if($user->aktif == 1){

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

											//dd($data);

											return redirect()->guest('home');
											
										}
										else{
											return redirect('login?error=invalid_user_profile4');
										}
										
									}
									else{
										return redirect('login?error=invalid_user_profile3');
									}
									
								}
								else{
									return redirect('login?error=invalid_user_profile2');
								}
								
							}
							else{
								return redirect('login?error=invalid_user_profile1');
							}
							
						}
						else{
							return redirect('login?error=invalid_auth_code2');
						}
						
					}
					else{
						return redirect('login?error=invalid_auth_code1');
					}
				
				}
				else{
					return redirect('login?error=invalid_state');
				}
				
			}
			else{
				return redirect('login?error=invalid_state');
			}
			
		}
		elseif(isset($_GET['error'])){ //error
			return redirect('login?error='.$_GET['error']);
		}
		else{
			return redirect('login');
		}
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

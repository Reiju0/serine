<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use DB;

class ProfileController extends Controller
{
    public function __construct(){
        $this->middleware('ajax');

    }
    //
    public function index(Request $request){
    	$user = Sentinel::check();
    	$roles = json_decode($user->roles);
    	$role = "";
    	foreach ($roles as $key) {
            $role .= $key->name.'; ';
        }

        $group_id = json_decode($user->group_id);
        $val = json_decode($user->group_value);
        $pic = $user->foto;
        $foto = "/images/avatar_noimage.png";
       
         
    	return view('profile', ['user'=>$user, 'role'=>$role, 'foto'=>$foto]);
    }

    public function update(Request $request){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'numeric'  => 'Kolom :attribute hanya boleh diisi angka.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'email'      => 'required',
            'telepon'    => 'numeric',
            'kdnip'     =>'numeric'     
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $user = Sentinel::check();
        $username       = PublicFunction::htmlTag($request->input('username'));
        $email          = PublicFunction::htmlTag($request->input('email'));
        $nama            = PublicFunction::htmlTag($request->input('nama'));
        $nama_unit        = PublicFunction::htmlTag($request->input('nama_unit'));
        $nip            = PublicFunction::htmlTag($request->input('nip'));
        $alamat         = PublicFunction::htmlTag($request->input('alamat'));
        $telepon        = PublicFunction::htmlTag($request->input('telepon'));
        $credentials = [
		    'email'       => $email,
            'nama'         => $nama,
		    'nama_unit'   => $nama_unit,
            
            'nip'     => $nip,
            'alamat'        => $alamat,
		    'telp'		=> $telepon
		];
		

			try{
				$user = Sentinel::update($user, $credentials);
                //app(PublicF::class)->activity_log($user->id, "G2: melakukan perubahan data user id: $user->id");
				return "success";
			}catch(Exception $e){
				return $e->getMessage();
			}


    }


    public function password(){
    	$user = Sentinel::check();
    	return view('password', ['user'=>$user]);
    }

    public function updatePassword(Request $request){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'old_password'  	=> 'required',
            'new_password'		=> 'required',
            're_new_password'	=> 'required'     
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $user = Sentinel::check();
        $credentials = [
		    'username' 	=> $user->username,
		    'password'	=> $request->input('old_password')
		];

		$check = Sentinel::validateCredentials($user, $credentials);

		if($check){
			$credentials = [
                'password' => $request->input('new_password'),
				'hpassword' => md5($request->input('new_password'))
			];
			$user = Sentinel::update($user, $credentials);
            //app(PublicF::class)->activity_log($user->id, "G2: melakukan perubahan password user id: $user->id");
			echo "success";
		}else{
			echo "password lama salah";
		}

    }
}

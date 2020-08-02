<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use DB;
use PublicFunction

class CreateController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.user_admin');
        $this->middleware('ajax');
    }

    public function index()
    {
    	# code...
    	return view('user/create');
    }

    public function create(Request $request)
    {
    	# code...
    	$messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'numeric'  => 'Kolom :attribute hanya boleh diisi angka.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'username'   => 'required',
            'email'      => 'required',
            'password'   => 'required',
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

        
        $id         = $request->input('id');
        $username   = PublicFunction::htmlTag($request->input('username'));
        $email   	= PublicFunction::htmlTag($request->input('email'));
        $nama            = PublicFunction::htmlTag($request->input('nama'));
        $jabatan        = PublicFunction::htmlTag($request->input('jabatan'));
        $kdnip           = PublicFunction::htmlTag($request->input('kdnip'));
        $nip            = PublicFunction::htmlTag($request->input('nip'));
        $nip2           = PublicFunction::htmlTag($request->input('nip2'));
        $alamat         = PublicFunction::htmlTag($request->input('alamat'));
        $telepon        = PublicFunction::htmlTag($request->input('telepon'));
        $password	= md5($request->input('password'));
        
        $role_id	= $request->input('role');
        $role 		= Sentinel::findRoleById($role_id);
        $group_id	= $role->group_id;
        $group_value= explode("\n", str_replace("\r", "", $request->input('kode')));

        $group = '0';

        if($group_id > 0){
            $group = array();
        	$group['all'][] = array( 'id' => $group_id, 'val'=> $group_value);
        	$group = json_encode($group);
        }
        
        if(count($group_value) <> 1){
            $nilai_group = 0;
        }else{
            $nilai_group = $group_value[0];
        }

		

		$check_username = [
			'username' => PublicFunction::htmlTag($username),
		];
		$check_username = Sentinel::findByCredentials($check_username);

		if(!($check_username)){
			$credentials = [
				'username'		=> $username,
			    'email' 		=> $email,
			    'nama'         => $nama,
            'jab_es1_kl'   => $jabatan,
            'kdnip'     => $kdnip,
            'nip'     => $nip,
            'nip2'     => $nip2,
            'alamat'        => $alamat,
            'telp'      => $telepon,
                'hpassword'     => $password,
			    'password'		=> $password,
			    'group_id'		=> $group_id,
			    'group_value'	=> $nilai_group,
			    'user_group'	=> $group,
                'aktif'         => 1
			];
			try{
				
                
                //$role = Sentinel::findRoleById($role_id);
                $slug = $role->slug;

				if($slug != 'SuperAdministrator'){ //because 1 is superadministrator. only 1 user that can have this role
					$user = Sentinel::registerAndActivate($credentials);
					$role->users()->attach($user);
                    DB::table('role_users')
                        ->where('user_id', $user->id)
                        ->where('role_id', $role_id)
                        ->update([
                            'user_groups' => $group
                        ]);
				}
				/*
				DB::table('role_users')->insert([
					'user_id'	=> $user->id,
					'role_id'	=> $role_id
					]);*/

                //app(PublicF::class)->activity_log($user->username, "G2: membuat username: $user->username dengan role $slug");
				return "success";
			}catch(Exception $e){
				return $e->getMessage();
			}
		}else{

				return "Username telah digunakan";

			
		}


    }	
}

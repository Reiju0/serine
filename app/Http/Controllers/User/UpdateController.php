<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicF;
use PublicFunction;

class UpdateController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.user_admin');
        $this->middleware('ajax');

    }

    public function index($id)
    {
    	if($id == 1){ //id 1 adalah superadmin. gak boleh diapa2in. :p
    		$user = Sentinel::check();
    		$id = $user->id;
    	}
    	
        $query = DB::table('users')
        	->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
        	->where('users.id', '=', $id)
        	->get();
        $role = 0;
        if(!($query[0]->role_id == NULL)){
        	$role = $query[0]->role_id;
        }

        $group = json_decode($query[0]->user_group);
        $arraykey = array();
        if(isset($group->all)){
            foreach ($group->all as $key) {
                if(in_array(0, $key->val)){
                }else{
                    $arraykey = $key->val;
                    //$kode_value = '';
                    break;
                }
            }
        }

        

        

        return view('user/update',['detail' => $query, 'role' => $role, 'kode' => $arraykey]);
    }

    public function update(Request $request)
    {
    	# code...
    	$messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'numeric'  => 'Kolom :attribute hanya boleh diisi angka.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'id'        => 'numeric',
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
        $role_id	= $request->input('role');
        $role       = Sentinel::findRoleById($role_id);
        $group_id   = $role->group_id;
        //$group_value= $request->input('group');
        $group_value= explode("\n", str_replace("\r", "", $request->input('group')));

        $group = 0;

        if($group_id > 0){
            $group = array();
            //$group['all'][] = array( 'id' => $group_id, 'val'=> array($group_value));
            $group['all'][] = array( 'id' => $group_id, 'val'=> $group_value);
            $group = json_encode($group);
        }

        if(count($group_value) <> 1){
            $nilai_group = 0;
        }else{
            $nilai_group = $group_value[0];
        }

        if($id == 1){ //id 1 adalah superadmin. gak boleh diapa2in. :p
    		$user = Sentinel::check();
    		$id = $user->id;
    	}
    	

        $user 	= Sentinel::findById($id);

        $query = DB::table('role_users')
                    ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('slug', 'SuperAdministrator')
                    ->where('user_id', $id)
                    ->count();
        if($query < 1){
            
    		$credentials = [
    		    'email' 		=> $email,
    		    'nama'         => $nama,
                'jab_es1_kl'   => $jabatan,
                'kdnip'     => $kdnip,
                'nip'     => $nip,
                'nip2'     => $nip2,
                'alamat'        => $alamat,
                'telp'      => $telepon,
                'group_id'      => $group_id,
                'group_value'   => $nilai_group,
                'user_group'         => $group
    		];
			try{
				

		
                $role = Sentinel::findRoleById($role_id);
                $slug = $role->slug;
												
                if($slug != 'SuperAdministrator'){ 
                    $user = Sentinel::update($user, $credentials);
                   DB::table('role_users')->where('user_id', '=', $id)->delete();
				   $role->users()->attach($user);
                }
				
                app(PublicF::class)->activity_log($id, "G2: melakukan update data user id: $id");
				return "success";
			}catch(Exception $e){
				return $e->getMessage();
			}
		}else{
			return "permission Denied";
		}
    }

    public function password($id){
    	if($id > 1){ //id 1 adalah superadmin. gak boleh diapa2in. :p
    		$user = Sentinel::findById($id);
    	}else{
    		$user = Sentinel::check();
    	}
    	return view('user/password', ['user'=>$user]);
    }

    public function updatePassword(Request $request){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
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

        $id         = $request->input('id');

        $user = Sentinel::findById($id);

            $query = DB::table('role_users')
                    ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('slug', 'SuperAdministrator')
                    ->where('user_id', $id)
                    ->count();
        if($query > 0){
            $user = Sentinel::check();
        }

			$credentials = [
                'hpassword' => md5($request->input('new_password')),
				'password' => md5($request->input('new_password'))
			];
			$user = Sentinel::update($user, $credentials);
            app(PublicF::class)->activity_log($id, "G2: melakukan penggantian password user id: $id");
			return "success";
    }
}

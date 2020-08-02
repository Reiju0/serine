<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicF;
use PublicFunction;
use PublicRef;

class UpdateController extends Controller
{
    //
    private $permission;
    private $url;
    public function __construct(){
        $this->permission = "Core.admin";
        $this->url  = url('/users');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request, $id){
        // $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        // if($validation == false){
        //     return "cannot change this user information";
        // }

        $data = Sentinel::findById($id);

        $ur = DB::table('role_users')->where('user_id', $id)->first();

        $role = DB::table('role_users')->where('user_id', $data->id)->first();

        $satker = DB::table('ref_satker')->where('status', '1')->orderBy('kdsatker', 'asc')->get();

        $option = [];
        foreach ($satker as $key) {
            # code...
            $option[] = ['val'   => $key->kdsatker, 'text' => $key->kdsatker.'. '.$key->nmsatker];
        }

        $array = [
            'title'     => "Update ".$data->username,
            'url'       => $this->url.'/update',
            'back'      => $this->url,
            'input'     => [
                ['type' => 'text', 'name' => 'id',  'value' => $id, 'readonly' => true],
                ['type' => 'text', 'name' => 'username', 'required' => true, 'readonly' => true, 'value' => $data->username],
                ['type' => 'email', 'name' => 'email', 'required' => true, 'value' => $data->email],
                ['type' => 'text', 'name' => 'nama', 'required' => true, 'value' => $data->nama],
                ['type' => 'text', 'name' => 'nip', 'text' => 'NIP', 'required' => true, 'value' => $data->nip],
                ['type' => 'textarea', 'name' => 'alamat', 'value' => $data->alamat],
                ['type' => 'text', 'name' => 'telepon', 'title' => 'Telp / Fax', 'value' => $data->telp],
                ['type' => 'template', 'name' => 'role', 'param' => 'select','option' => [$role->role_id]],
                ['type' => 'select', 'title' => 'kode satker', 'name' => 'kdsatker', 'option' => $option, 'value' => $ur->user_groups],
            ],
        ];

        return PublicFunction::update_template($array);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'numeric',
            'username'  => 'alpha_dash',
            'email'     => 'required|email',
            'telepon'   => 'nullable|numeric',
            'kdnip'     => 'nullable|alpha_dash',     
        ], PublicRef::Validation_Message());

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
        $nip            = PublicFunction::htmlTag($request->input('nip'));
        $alamat           = PublicFunction::htmlTag($request->input('alamat'));
        $telepon        = PublicFunction::htmlTag($request->input('telepon'));


        $role_id    = $request->input('role');
        $role       = DB::table('roles')->where('id', $role_id)->first();
        $group_id   = $role->group_id;
        // $group_value= $request->input('kode');
        $role       = Sentinel::findRoleById($role_id);
        $group = '0';
        

        if($group_id > 0){
            $group= $request->input('kdsatker');
        }


        // $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        // if($validation == false){
        //     return "cannot change this user information";
        // }

        $user 	= Sentinel::findById($id);

        DB::beginTransaction();

		$query = DB::table('role_users')
                    ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('slug', 'SuperAdministrator')
                    ->where('user_id', $id)
                    ->count();
        if($query < 1){

    		$credentials = [
    		    'email' 		=> $email,
    		    'nama'         => $nama,
                'nip'     => $nip,
                'alamat'     => $alamat,
                'telp'      => $telepon

    		];
			try{
                $role_id    = $request->input('role');
                $role       = Sentinel::findRoleById($role_id);
                DB::table('users')->where('id', $id)->where('username', $username)->update($credentials);
                //$user = Sentinel::update($user, $credentials);
                DB::table('role_users')->where('user_id', $user->id)->delete();
                $role->users()->attach($user);
                DB::table('role_users')
                    ->where('user_id', $user->id)
                    ->where('role_id', $role_id)
                    ->update([
                        'user_groups' => $group
                    ]);


                DB::commit();
				
			}catch(Exception $e){
				return 'error occurred';
			}
            return "success";
		}else{
			return "permission Denied";
		}
    }

    public function password(Request $request, $id){

        // $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        // if($validation == false){
        //     return "cannot change this user information";
        // }

        $data = Sentinel::findById($id);

        $array = [
            'title'     => "Update ".$data->username,
            'url'       => $this->url.'/password',
            'back'      => $this->url,
            'input'     => [
                ['type' => 'text', 'name' => 'id', 'required' => true, 'value' => $id, 'readonly' => true],
                ['type' => 'text', 'name' => 'username', 'required' => true, 'readonly' => true, 'value' => $data->username],
                ['type' => 'password', 'name' => 'password', 'required' => true],
                ['type' => 'password', 'name' => 'repassword', 'text' => 'Re Password', 'required' => true],
            ],
        ];

        return PublicFunction::update_template($array);

    }

    public function updatePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'id'        => 'numeric',
            'username'  => 'alpha_dash',
            'new_password'		=> 'required',
            're_new_password'	=> 'required'     
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $id         = $request->input('id');

        // $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        // if($validation == false){
        //     return "cannot change this user information";
        // }
        
        	$user = Sentinel::findById($id);

            $query = DB::table('role_users')
                    ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('slug', 'SuperAdministrator')
                    ->where('user_id', $id)
                    ->count();
        if($query > 0){
        	$user = $request->user;
        }

			$credentials = [
                'password' => $request->input('new_password'),
				'hpassword' => md5($request->input('new_password'))
			];
			$user = Sentinel::update($user, $credentials);

            //app(PublicF::class)->activity_log($id, "G2: melakukan update password user id: $id");
			echo "success";
    }
}

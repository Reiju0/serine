<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicFunction;
use PublicRef;

class PasswordController extends Controller
{
    
    private $permission;
    private $url;
    public function __construct(){
        $this->permission = "Users.admin";
        $this->url  = url('/users');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }


    public function password(Request $request, $id){
    	$validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        $data = Sentinel::findById($id);

        $array = [
            'title'     => "Update ".$data->username,
            'url'       => $this->url.'/password',
            'back'      => $this->url,
            'input'     => [
                ['type' => 'text', 'name' => 'id', 'required' => true, 'value' => $id, 'readonly' => true],
                ['type' => 'text', 'name' => 'username', 'required' => true, 'readonly' => true, 'value' => $data->username],
                ['type' => 'password', 'name' => 'password', 'required' => true, 'value' => ''],
                ['type' => 'password', 'name' => 'repassword', 'text' => 'Re Password', 'required' => true, 'value' => ''],
            ],
        ];

        return PublicFunction::update_template($array);
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'id'        => 'numeric',
            'username'  => 'alpha_dash',
            'password'      => 'required',
            'repassword'   => 'required'     
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

        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }
        
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
                'password' => $request->input('password'),
				'hpassword' => md5($request->input('password'))
			];
			$user = Sentinel::update($user, $credentials);
            
			return "success";
    }
}

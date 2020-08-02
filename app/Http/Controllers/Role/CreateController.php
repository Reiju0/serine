<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicFunction;

class CreateController extends Controller
{
    //
    public function index(){
    	return view('role/create');
    }

    public function create(Request $request){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'role'      => 'required'      
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $role 	= PublicFunction::htmlTag($request->input('role'));
        $slug 	= preg_replace('/\s+/', '', str_replace(' ', '', $role));

        $check =  Sentinel::findRoleBySlug($slug);


        if($check){
        	echo"Role Sudah Ada";
        }else{
        	try{
	        	$role = Sentinel::getRoleRepository()->createModel()->create([
				    'name' => $role,
				    'slug' => $slug,
                    'group_id' => $request->input('groups'),
				]);
				echo "success";
        	}catch(Exception $e){
                echo $e->getMessage();
            } 
        	
        }     
     
    }
    
}

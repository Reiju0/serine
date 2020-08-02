<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use DB;
use PublicFunction;

class CreateController extends Controller
{
    //
    public function index()
    {
    	# code...
    	return view('permission/create');
    }

    public function create(Request $request){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'module'      => 'required',
            'permission'       => 'required'       
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $module       = PublicFunction::htmlTag($request->input('module'));
        $permission   = PublicFunction::htmlTag($request->input('permission'));
        $permission_name = $module.'.'.$permission;
            
        $check = DB::table('permission')
        	->where('module', '=', $module)
        	->where('permission', '=', $permission_name)
        	->count();

        if($check < 1){
            try{
                DB::table('permission')->insert([
                        'module'          => $module,
                        'permission'      => $permission_name
                        ]
                );
                echo "success";
            }catch(Exception $e){
                echo $e->getMessage();
            } 
        }else{
        	echo "Permission Sudah ada.";
        }      


        
    }
}

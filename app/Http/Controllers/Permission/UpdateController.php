<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use DB;
use PublicFunction;

class UpdateController extends Controller
{
    //
    public function index($id)
    {
    	
        $query = DB::table('permission')
        	->where('id', '=', $id)
        	->get();
      	$permission = explode(".", $query[0]->permission, 2);
        return view('permission/update',['detail' => $query, 'permission' => $permission[1]]);
    }

    public function update(Request $request)
    {
    	# code...
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

        $id         = $request->input('id');
        $module       = PublicFunction::htmlTag($request->input('module'));
        $permission   = PublicFunction::htmlTag($request->input('permission'));
        $permission_name = $module.'.'.$permission;

        $check = DB::table('permission')
        	->where('module', '=', $module)
        	->where('permission', '=', $permission_name)
        	->where('id', '<>', $id)
        	->count();

        if($check < 1){
            try{
                DB::table('permission')->where('id', $id)
                ->update([
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

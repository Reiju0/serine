<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicFunction;

class PermissionController extends Controller
{
    private $permission;
    private $url;
    public function __construct(){
        $this->permission = "Users.admin";
        $this->url  = url('/users');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    //
    public function permission(Request $request, $id)
    {
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }
        # code...
        
        $users = Sentinel::findById($id);

        $permission = $users->permissions;
        //dd($users);

        $all = DB::table('permission')->orderBy('permission', 'asc')->get();
        
        $dokumen = DB::table('ref_jendok_dokumentasi')->get();
        return view('users::permission', ['all' => $all,'permission'=>$permission,'users'=>$users, 'no'=>0, 'no2' =>0, 'dokumen' => $dokumen]);
    }

    public function permissionUpdate(Request $request, $id){
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }
        
        $permission = $request->input('permission');

        $array = array();

        foreach ($permission as $key) {
            # code...
            if(isset($key['value']) == 1){
                $array[$key['name']] = true;
            }
        }

        $array = json_encode((object) $array);
        //dd($admins);

        $query = DB::table('users')
                 ->where('id', $id);
        try{
            $query->update([
                'permissions' => $array
            ]);


            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        }


        //$json = json_encode($array);
    }
}

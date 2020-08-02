<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicFunction;

class UpdateController extends Controller
{
    //
    public function index($id)
    {
    	# code...
    	$role = Sentinel::findRoleById($id);
    	//dd($role);

        return view('role/update',['item' => $role]);
    }

    public function update(Request $request)
    {
    	# code...
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

        $id 	= $request->input('id');
        $group 	= $request->input('groups');
        $role 	= PublicFunction::htmlTag($request->input('role'));
        $slug 	= preg_replace('/\s+/', '', str_replace(' ', '', $role));

        $role = Sentinel::findRoleById($id);
        $slug = $role->slug;

        if($slug != 'SuperAdministrator'){ //id 1 adalah superadmin. gak boleh diapa2in. :p
        	$query = DB::table('roles')
                    ->where('id', $id);
    		try{
    			$query->update([
                        'group_id'          => $group,
                        ]);

    			echo "success";
            }catch(Exception $e){
                echo $e->getMessage();
            }
    	}else{
    		echo "can't do that";
    	}
    	
    }

    public function permission($id)
    {
        # code...
        
        $role = Sentinel::findRoleById($id);
        $permission = $role->permissions;
        if(empty($role->admin_of)){
            $admin = array();
        }else{
            $admin = json_decode($role->admin_of, true);            
        }

        $slug = $role->slug;
        //dd($admin);

        

        if($slug == 'SuperAdministrator'){
            exit();
        }
        $all = DB::table('permission')->orderBy('permission', 'asc')->get();
        $roles = DB::table('roles')->whereNotIn('slug', ['SuperAdministrator'])->orderBy('name')->get();
        
        return view('role/permission', ['all' => $all,'permission'=>$permission,'role'=>$role, 'no'=>0, 'roles' => $roles, 'no2' =>0, 'admin'=>$admin]);
    }

    public function permissionUpdate(Request $request){
        $id = $request->input('id');
        $permission = $request->input('permission');
        $admin = $request->input('admin');

        $array = array();
        $admins = array();

        $role = Sentinel::findRoleById($id);

        foreach ($permission as $key) {
            # code...
            if(isset($key['value']) == 1){
                $array[$key['name']] = true;
            }
        }

        foreach ($admin as $key) {
            # code...
            if(isset($key['value']) == 1){
                //$admins[$key['name']] = true;
                $admins[] = $key['name'];
            }
        }

        $admins = json_encode((object) $admins);
        $array = json_encode((object) $array);
        //dd($admins);

        $query = DB::table('roles')
                 ->where('id', $id);
        try{
            $query->update([
                'permissions' => $array,
                'admin_of'       => $admins,
            ]);

            //$role->permissions = $array;
            //$role->save();

            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        }


        //$json = json_encode($array);
    }
}

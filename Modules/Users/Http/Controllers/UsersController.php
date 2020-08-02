<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PublicFunction;
use DB;

class UsersController extends Controller
{
    private $permission;
    private $url;
    private $roleJafung;
    public function __construct(){
        $this->permission = "Users.admin";
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');
        $this->roleJafung = ['2', '3', '4', '5'];

    }
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function userValidation(Request $request, $roleId, $val){
        $user   = Sentinel::check();
        $role   = Sentinel::findById($user->id)->roles;
        $admin_of = [];

        $return = true;
        //dd($role);

        foreach ($role as $key) {
            # code...
            if(!empty($key->admin_of)){
                $array = json_decode($key->admin_of, true);
                foreach ($array as $keys) {
                    # code...
                    $admin_of[] = $keys;
                }
            } 
        }

        if($request->group != 'all'){
            if(!in_array($roleId, $admin_of)){
                $return = false;
            }
        }

        $role = DB::table('roles')->where('id', $roleId)->first();

        if($role->group_id != 0){
            
            //dd($role);
            $group = DB::table('user_group')->where('id', $role->group_id)->first();

            if($role->group_id == 1){
                $sql = "SELECT a.*, b.kdkanwil from t_satker a left join t_kppn b on a.kdkppn = b.kdkppn where a.kdsatker = ?";
            }else{
                $sql = "SELECT * FROM $group->ref_table where $group->kolom = ?";
            }
            
            $query = DB::table(DB::raw("($sql) a"))
                    ->setBindings([$val]);

            $query = PublicFunction::WhereGroup($request->group, $query);

            //dd($query->getBindings());
            if($query->count() < 1){
                $return = false;
            }

            //$query = PublicFunction::WhereGroup($request->group, $query);
        }


        return $return;
        //dd('ko');
    }

    public function DeleteData(Request $request, $userId, $roleId, $val)
    {
        $cek = $this->userValidation($request, $roleId, $val);
        if($cek){
            
            $delete     = true;
            $data       = null;
            if($val != 0){
                $current = DB::table('role_users')
                        ->where('user_id', $userId)
                        ->where('role_id', $roleId)
                        ->first();

                $data   = json_decode($current->user_groups);
                $new_data = array();

                /*cek data on active group*/
                $new_val    = array();
                if(isset($data->all)){
                    if(in_array($val, $data->all[0]->val)){
                        if (($key = array_search($val, $data->all[0]->val)) !== false) {
                            unset($data->all[0]->val[$key]);
                        }
                    }

                    foreach ($data->all[0]->val as $key) {
                        array_push($new_val, $key);
                    }

                    $data->all[0]->val = $new_val;
                }
                
                if(count($new_val) > 0){
                    $delete = false;
                    $new_data['all'] = $data->all;
                }

                /*cek data on inactive group*/
                $new_val    = array();
                if(isset($data->off)){
                    if(in_array($val, $data->off[0]->val)){
                        if (($key = array_search($val, $data->off[0]->val)) !== false) {
                            unset($data->off[0]->val[$key]);
                        }
                    }

                    foreach ($data->off[0]->val as $key) {
                        array_push($new_val, $key);
                    }

                    $data->off[0]->val = $new_val;
                }
                if(count($new_val) > 0){
                    $delete = false;
                    $new_data['off'] = $data->off;
                }
            }

            if($delete){
                DB::table('role_users')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->delete();
            }else{
                DB::table('role_users')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->update([
                        'user_groups' => json_encode($new_data)
                    ]);
            }


            return 'success';
        }else{
            return "permission denied";
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function EnableData(Request $request, $userId, $roleId, $val)
    {
        $cek = $this->userValidation($request, $roleId, $val);
        if($cek){
            $current = DB::table('role_users')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->first();

            $role   = DB::table('roles')->where('id', $roleId)->first();

            $data       = json_decode($current->user_groups);
            $new_val    = array();
            $new_data   = array();
            if(isset($data->off)){
                if(in_array($val, $data->off[0]->val)){

                    if(isset($data->all)){
                        if(!in_array($val, $data->all[0]->val)){
                            array_push($data->all[0]->val, $val);
                        }
                    }else{
                        $new_data['all'][] = ['id' => $role->group_id, 'val' => [$val]];
                    }
                    if (($key = array_search($val, $data->off[0]->val)) !== false) {
                        unset($data->off[0]->val[$key]);
                    }
                }

                foreach ($data->off[0]->val as $key) {
                    array_push($new_val, $key);
                }

                if(count($new_val) > 1){
                    $new_data['off'][] = ['id' => $role->group_id, 'val' => [$new_val]];
                }

                if(in_array($roleId, $this->roleJafung)){
                    $new_data = array();
                    $new_data['all'][] = ['id' => $role->group_id, 'val' => [$val]];
                    $sql = DB::table('role_users')
                        ->where('user_id', $userId)
                        ->where('role_id', '<>', $roleId)
                        ->whereIn('role_id', $this->roleJafung)
                        ->delete();
                    //dd($sql);
                }
                

                DB::table('role_users')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->update([
                        'user_groups' => json_encode($new_data)
                    ]);

                return 'success';
            }

        }else{
            return "permission denied";
        }
 
    }

    
}

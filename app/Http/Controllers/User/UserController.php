<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PublicF;

class UserController extends Controller
{
    //
    public function getRole($role_id){
        $query = DB::table('roles')->where('slug', '<>', 'SuperAdministrator');
        //$query = app(PublicF::class)->WhereRdid($query, 'ref_daerah');
        $query = $query->get();
        
        $role = "<option value='0'>null</option>";
        //$role = "";
        foreach ($query as $key) {
            $role .= "<option value='".$key->id."' ";
            if($key->id == $role_id ){
                $role .= "selected";
            }
            $role .= ">".$key->name."</option>";
        }
        return $role;
    }

    public function DeleteData($id){
        //perlu dicek lagi

        try{
            if($id > 1){
                //$user = Sentinel::findById($id);
                //$user->delete();
                DB::table('users')->where('id', $id)->update([
                    'aktif'          => ''
                    ]);
                app(PublicF::class)->activity_log($id, "G2: melakukan penonaktifan user id: $id");
                return "success";
            }else{
                return "User tidak dapat dihapus";
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        } 

    }

    public function EnableData($id){
        //perlu dicek lagi

        try{
            if($id > 1){
                //$user = Sentinel::findById($id);
                //$user->delete();
                DB::table('users')->where('id', $id)->update([
                    'aktif'          => '1'
                    ]);
                app(PublicF::class)->activity_log($id, "G2: melakukan pengaktifan user id: $id");
                return "success";
            }else{
                return "User tidak dapat diaktifkan";
            }
            
        }catch(Exception $e){
            return $e->getMessage();
        } 

    }

    public function getRoleGroup($id, $selected){
        $role = Sentinel::findRoleById($id);
        $all = "none";
        //dd($role);
        //$all = "<option value='0'>All</option>";
        if($role->group_id > 0){
            $all = "<option value='0'>All</option>";
            $user_group = DB::table('user_group')
                            ->where('id', '=', $role->group_id)
                            ->first();

            $ref_table = DB::table($user_group->ref_table)
                        ->get();

            foreach ($ref_table as $key) {
                $array =  (array) $key;
                $all .= "<option value='".$array[$user_group->kolom]."' ";
                if($array[$user_group->kolom] == $selected ){
                    $all .= "selected";
                }
                $all .= ">".$array[$user_group->kolom].'. '.$array[$user_group->nama]."</option>";
            }
            
           // dd($ref_table);
        }
        return $all;
    }
}

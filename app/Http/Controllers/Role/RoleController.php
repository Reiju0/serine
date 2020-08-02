<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class RoleController extends Controller
{
    //
    public function getUserGroup($id)
    {
    	# code...
    	$query = DB::table('user_group')->get();
        
        $group = "<option value='0'>No Group (have access to All)</option>";
        foreach ($query as $key) {
            $group .= "<option value='".$key->id."' ";
            if($key->id == $id ){
                $group .= "selected";
            }
            $group .= ">".$key->name."</option>";
        }
        return $group;
    }

    public function DeleteData($id){
        //perlu dicek lagi

        try{
            $role = Sentinel::findRoleById($id);
            $slug = $role->slug;

            if($slug != 'SuperAdministrator'){
                
                $role->delete();
                echo "success";
            }else{
                echo "Role tidak dapat dihapus";
            }
            
        }catch(Exception $e){
            echo $e->getMessage();
        } 

    }

    
}

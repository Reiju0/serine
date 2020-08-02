<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Module;
use DB;

class PermissionController extends Controller
{
    //
    public function getModule($mod_name){
        $module = Module::all();

        /*foreach ($module as $key) {
            # code...
            echo $key->name;
        }*/
        
        $list = "<option value='Core'>Core</option>";
        foreach ($module as $key) {
            $list .= "<option value='".$key->name."' ";
            if($key->name == $mod_name ){
                $list .= "selected";
            }
            $list .= ">".$key->name."</option>";
        }
        return $list;
    }

    public function DeleteData($id){
        
        $query = DB::table('permission')
            ->where('id', $id);
       
        try{
            $query->delete();
            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        } 

    }
}

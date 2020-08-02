<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class MenuController extends Controller
{
    //
    public function getParent($parent_id){
        $query = DB::table('menu')->where('is_parent', '=', 1)->get();
        $parent = "<option value='0'>null</option>";
        //$role = "";
        foreach ($query as $key) {
            $parent .= "<option value='".$key->id."' ";
            if($key->id == $parent_id ){
                $parent .= "selected";
            }
            $parent .= ">".strip_tags($key->menu)."</option>";
        }
        return $parent;
    }
   

}

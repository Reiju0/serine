<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class DeleteController extends Controller
{
    //
    public function DeleteData($id){
        $query = DB::table('menu')
            ->where('id', $id);
       
        try{
            $query->delete();
            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        } 

    }
   

}

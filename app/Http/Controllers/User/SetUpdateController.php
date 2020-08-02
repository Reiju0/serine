<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicF;

class SetUpdateController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:superadmin');

    }

    

    public function setpassword($lower, $higher){
        $query = DB::table('users')
                ->whereNotNull('hpassword')
                ->whereNull('password')
                ->orderBy('id', 'asc')
                ->offset($lower)
                ->limit($higher)
                ->get();

        foreach ($query as $key) {
            # code...
            echo $key->id.' - '.$key->username.' - '.$key->hpassword.'<br>';
            

            $user = Sentinel::findById($key->id);
            //dd($user);
            $credentials = [
                'password' => $key->hpassword
            ];
            $user = Sentinel::update($user, $credentials);
        }

    }
}

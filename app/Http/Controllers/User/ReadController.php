<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use PublicF;

class ReadController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.user_admin');
        $this->middleware('ajax');

    }

    public function index(){
    	return view('user/read');
    }

    public function data(Request $request)
    {
        
        $GLOBALS['nomor'] = 1 + $request->get('start');
        $query = DB::table('users')
        	->select(['users.id', 'users.username', 'users.email', 'users.first_name', 'users.last_name', 'roles.name as role', 'users.group_value', 'roles.id as roleid', 'users.aktif'])
        	->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
        	->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
        	->where(function ($query) {
                $query->whereNull('roles.name')
                      ->orwhere('roles.id', '!=', '0');
            })
            ->where(function($query){
                $query->whereNull('roles.slug')
                      ->orwhere('roles.slug', '<>', 'SuperAdministrator');
            });
        

        return Datatables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('username')) {
                    $query->where('username', 'like', "%{$request->get('username')}%");
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }

                if ($request->has('kode')) {
                    $query->where('group_value', 'like', "%{$request->get('kode')}%");
                }

                if ($request->has('role')) {
                    if($request->get('role') != 'x'){
                        $query->where('roles.id', '=', $request->get('role'));
                    }
                }

                if ($request->has('status')) {
                    if($request->get('status') == 1){
                        $query->where('aktif', 1)->whereNotNull('aktif');
                    }else if($request->get('status') == 2){
                        $query->where(function($query){
                            $query->whereNull('aktif')->orWhere('aktif', '<>', '1');
                        });
                        
                    }
                }
            })
        	->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query) {
            	$action = '<a href="javascript:ajaxLoad(\''.url('/user/update/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
                $action .= '<a href="javascript:ajaxLoad(\''.url('/user/password/'.$query->id).'\')" class="btn btn-xs btn-info" id="'.$query->id.'"><i class="fa fa-lock"></i> password</a>';
                if($query->aktif == 1){
            	   $action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->id.')"><i class="fa fa-trash"></i> delete</a>';
                }else{
                    $action .= '<a class="btn btn-xs btn-primary" id="'.$query->id.'" onclick="data_enable('.$query->id.')"><i class="fa fa-trash"></i> Enable </a>';
                }
            	return $action;
            })
            ->make(true);


    }
}

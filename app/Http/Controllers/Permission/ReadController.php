<?php

namespace App\Http\Controllers\Permission;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use DataTables;

class ReadController extends Controller
{
    //
    public function index(){
    	return view('permission/read');
    }

    public function data(Request $request)
    {
        
        $GLOBALS['nomor'] = 1 + $request->get('start');
        $query = DB::table('permission')
            ;
        
        //dd($query->get());

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('module')) {
                    $query->where('module', 'like', "%{$request->get('module')}%");
                }

                if ($request->has('permission')) {
                    $query->where('permission', 'like', "%{$request->get('permission')}%");
                }
            })
        	->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query) {
            	$action = '<a href="javascript:ajaxLoad(\''.url('/permission/update/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
            	$action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->id.')"><i class="fa fa-trash"></i> delete</a>';
            	return $action;
            })
            ->make(true);


    }
}

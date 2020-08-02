<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use DataTables;
use PublicFunction;

class ReadController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.menu_admin');
        $this->middleware('ajax');

    }

    public function index(Request $request){

        $array = [
            'title'  => 'Menu',
            'url'    => url('/menu/data'),
            'file_name' => 'daftar menu',
            'data'   => [
                ['title' => 'id', 'name' => 'id' ],
                ['title' => 'Menu', 'name' => 'menu' ],
                ['title' => 'Is Parent', 'name' => 'is_parent' ],
                ['title' => 'Parent Id', 'name' => 'parent_id' ],
                ['title' => 'Urutan', 'name' => 'delta' ],
                ['title' => 'Permission', 'name' => 'permission_break' ],
                ['title' => 'Status', 'name' => 'status' ],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'search' => [
                ['type' => 'text', 'name' => 'nmmenu', 'text' => 'Menu'],
                ['type' => 'select', 'name' => 'parent', 'text' => 'Is Parent', 'option' => 
                    [
                        ['val' => '', 'text' => 'All'],
                        ['val' => '1', 'text' => 'Parent'],
                        ['val' => '0', 'text' => 'Child'],
                    ]
                ],

            ]
            
        ];

        $permission = PublicFunction::permission($request, array('Core.admin'));
        if ($permission['response'] == true)
        {
        $addarray  = ['create'    => url('/menu/create')];
        $array = (object) array_merge((array) $array, (array) $addarray);
        }

        return PublicFunction::datatables_template($array, $request->group);
        //return view('users::read');
    }

    public function indexs(){
       
    	return view('menu/read');
    }

    public function data(Request $request)
    {
        
        $GLOBALS['nomor'] = 1 + $request->get('start');
        $query = DB::table('menu');
        
        //dd($query->get());

        return DataTables::of($query)
        	->filter(function ($query) use ($request) {
                // if ($request->has('menu') and $request->get('menu') != '') {
                //     $query->where('parent_id', '=', $request->get('menu'));
                // }

                if ($request->has('parent') and $request->get('parent') != '') {
                    $query->where('is_parent', '=', $request->get('parent'));
                }

                if ($request->has('nmmenu')) {
                    $query->whereRaw('upper(menu) like upper(?)',"%{$request->get('nmmenu')}%");
                }
            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('permission_break', function($query){
                $permission = str_replace(",",", ",$query->permission);
                return $permission;
            })
            ->addColumn('action', function ($query) {
            	$action = '<a href="javascript:ajaxLoad(\''.url('/menu/update/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
            	$action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->id.')"><i class="fa fa-trash"></i> delete</a>';
            	return $action;
            })
            ->make(true);


    }
}

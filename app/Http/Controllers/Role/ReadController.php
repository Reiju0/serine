<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use DataTables;
use PublicFunction;


class ReadController extends Controller
{
    //
    private $permission;
    private $url;
    public function __construct(){
        $this->permission = "Core.role_admin";
        $this->url  = url('/role');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request){
        $array = [
            'title'  => 'Daftar Role ',
            'url'    => $this->url.'/data',
            'file_name' => 'daftar role',
            'data'   => [
                ['title' => 'role', 'name' => 'name' ],
                ['title' => 'slug', 'name' => 'slug' ],
                ['title' => 'permission', 'name' => 'json_permission' ],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'javascript' => "function data_delete(id, slug){
                    alertify.prompt('Untuk menghapus role ini, masukkan: '+slug, 
                        function(evt, value) { 
                            if(value==slug){
                                $.ajax({
                                    type: 'GET',
                                    url: '".url('/role/delete')."/'+id,
                                    contentType: false,
                                    success: function (data) {
                                        if(data == 'success'){
                                            alertify.log('data berhasil dihapus');
                                            ajaxLoad('".url('/role')."');
                                        }else{
                                            alertify.log(data);
                                        }
                                        
                                    }
                                });
                            }else{
                                alertify.error('Kode salah');
                            }
                        });
                }"
        ];

        $permission = PublicFunction::permission($request, array('Core.role_admin'));
        if ($permission['response'] == true)
        {
        $addarray  = ['create'    => $this->url.'/create'];
        $array = (object) array_merge((array) $array, (array) $addarray);
        }

        return PublicFunction::datatables_template($array, $request->group);
        //return view('users::read');
    }

    public function data(Request $request)
    {
        
        $GLOBALS['nomor'] = 1 + $request->get('start');

        $sql = 'select a.*
                FROM (
                    select a.*
                        From roles a
                        where a.slug <> ?
                ) a';
        $query = DB::table(DB::raw("($sql) a"))
                ->setBindings(['SuperAdministrator']);
       
        //dd($query->get());

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
            })
        	->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('json_permission',function($query){
                $permission = $query->permissions;
                $permission = str_replace(",",", ",$permission);

                return $permission;
            })
            ->addColumn('action', function ($query) {
                $action = '<a href="javascript:ajaxLoad(\''.url('/role/permission/'.$query->id).'\')" class="btn btn-xs btn-info" id="'.$query->id.'"><i class="fa fa-pencil"></i> permission</a>';
            	$action .= '<a href="javascript:ajaxLoad(\''.url('/role/update/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
            	$action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->id.',\''.$query->slug.'\')"><i class="fa fa-trash"></i> delete</a>';
            	return $action;
            })
            ->make(true);


    }
}

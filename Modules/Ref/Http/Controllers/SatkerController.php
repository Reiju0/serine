<?php

namespace Modules\Ref\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use DataTables;
use DB;

class SatkerController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
    public function __construct(){
        $this->permission = "Ref.admin,Ref.read";
        $this->table = 'ref_satker';
        $this->kdkey = 'kdsatker';
        $this->nmkey = 'nmsatker';
        $this->title = 'Ref SATKER';
        $this->url  = url('/ref/satker');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request){
        // dd($request->group);
        $array = [
            'title'  => 'Referensi Satker',
            'url'    => $this->url.'/data',
            'file_name' => 'daftar satker',
            'data'   => [
                ['title' => 'KD Dept', 'name' => 'kddept' ],
                ['title' => 'KD Unit', 'name' => 'kdbaes1' ],
				['title' => 'Kode Satker', 'name' => 'kdsatker' ],
                ['title' => 'Nama Satker', 'name' => 'nmsatker' ],
                ['title' => 'KD KPPN', 'name' => 'kdkppn' ],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'search' => [

                ['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
                ['type' => 'text', 'name' => 'nmsatker', 'text' => 'Nama Satker'],

                ['type' => 'select', 'name' => 'status', 'text' => 'Pilih Jenis', 'option' => 
                    [
                        ['val' => '1', 'text' => 'Aktif'],
                        ['val' => '0', 'text' => 'Tidak Aktif'],
                    ]
                ],
            ]
        ];

        $permission = PublicFunction::permission($request, array('Ref.admin'));
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

        $user   = $request->user;
        
        $GLOBALS['nomor'] = 1 + $request->get('start');


        $sql = $this->table;

        $query = DB::table($sql);
		

        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {

              

                 if ($request->has('kdsatker')) {

                    $query->whereRaw('lower(kdsatker) like lower(?) ',["%{$request->get('kdsatker')}%"]);
                }
                 if ($request->has('nmsatker')) {

                    $query->whereRaw('lower(nmsatker) like lower(?) ',["%{$request->get('nmsatker')}%"]);
                }


                if ($request->has('status')) {
                    $query->where('status', $request->get('status'));
                }

            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query) use ($request){
                $action = "";
                $tkdkey = $this->kdkey;
                $permission = PublicFunction::permission($request, array('Ref.admin'));
                if ($permission['response'] == true)
                {
                    $action = '<a href="javascript:ajaxLoad(\''.url($this->url.'/update/'.$query->$tkdkey).'\')" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i> update</a>';
                    if($query->status == 1){
                        $action .= '<a class="btn btn-xs btn-danger" href="javascript:ajaxLoad(\''.url($this->url.'/disable/'.$query->$tkdkey).'\')"><i class="fa fa-trash"></i> Disable</a>';    
                    }else{
                        $action .= '<a class="btn btn-xs btn-primary" href="javascript:ajaxLoad(\''.url($this->url.'/enable/'.$query->$tkdkey).'\')"><i class="fa fa-trash"></i> Enable </a>';
                    }
                }

                return $action;
            })
            ->make(true);


    }
}
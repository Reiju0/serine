<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use DataTables;
use DB;

class DashboardSatkerController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $kdsatker;
    private $title;
    private $status;
    public function __construct(){

        $this->permission = "Dashboard.satker_read";
        $this->table = 'todo';
        $this->kdkey = 'id';
        $this->kdsatker = 'kdsatker';
        $this->status = 'status';
        $this->title = 'Todo List';
        $this->url  = url('/dashboard/satker');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request){
        $array = [
            'title'  => $this->title,
            'url'    => $this->url.'/data',
            'file_name' => 'daftar '.$this->title,
            'data'   => [
                ['title' => 'Jenis', 'name' => 'jenis' ],
                ['title' => 'Sifat', 'name' => 'priority_color' ],
                ['title' => 'Deskripsi', 'name' => 'description' ],
                ['title' => 'Batas Waktu', 'name' => 'f_due' ],
                ['title' => 'Tanggal Selesai', 'name' => 'f_done' ],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'search' => [
                ['title'=> 'Jenis To Do', 'type' => 'template', 'name' => 'getRefTableAdvance','param' => 'select','delta' => 'kdjenis', 'option' =>
                    ['select * from ref_jenis where status=1 order by kdjenis', 'kdjenis','kdjenis', 'jenis', 0, false]],
                ['type' => 'select', 'name' => 'priority', 'text' => 'Pilih Sifat', 'option' =>
                    [
                        ['val' => '', 'text' => '--Pilih Sifat--'],
                        ['val' => '1', 'text' => 'Biasa'],
                        ['val' => '2', 'text' => 'Segera'],
                        ['val' => '3', 'text' => 'Sangat Segera'],
                    ]
                ],
                ['type' => 'select', 'name' => 'status', 'text' => 'Pilih Status', 'option' =>
                    [
                        ['val' => '0', 'text' => 'Belum selesai'],
                        ['val' => '1', 'text' => 'Selesai'],
                    ]
                ]

            ]

        ];

        /*$permission = PublicFunction::permission($request, array('Dashboard.satker_create'));

        if ($permission['response'] == true)
        {
            $addarray  = ['create'    => $this->url.'/create'];
            $array = (object) array_merge((array) $array, (array) $addarray);
        }*/

        return PublicFunction::datatables_template($array, $request->group);
    }

    public function data(Request $request)
    {
        $user   = $request->user;
        $GLOBALS['nomor'] = 1 + $request->get('start');


        $sql = "select a.*,
		               date_format(a.due_date,'%d-%m-%Y') f_due,
		               date_format(a.done_date,'%d-%m-%Y') f_done,
                       b.jenis
					from todo a
                left join ref_jenis b 
				on a.kdjenis=b.kdjenis";

        $query = DB::table(DB::raw("($sql) a"))
            ->whereNull('deleted_at')
            ->selectRaw('a.*');

        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('kdjenis') && $request->get('kdjenis')!="") {
                    $query->where('kdjenis', $request->get('kdjenis'));
                }
                if ($request->has('priority') && $request->get('priority') != '') {
                    $query->where('priority', $request->get('priority'));
                }
                if ($request->has('status') && $request->get('status') != '') {
                    $query->where('status', $request->get('status'));
                }

            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query) use ($request){
                $action='';
				
				if($query->status=='0'){
					$action .= '<a href="javascript:ajaxLoad(\''.url($this->url.'/done/'.$query->id).'\')" class="btn btn-xs btn-success">
                            <i class="fa fa-calendar-check-o"></i> Selesai</a>';
				}

                return $action;
            })
			->addColumn('priority_color', function ($query) {
				if($query->priority=='1'){
					return '<span class="badge badge-primary mb-1 mr-2">Biasa</span>';
				}elseif($query->priority=='2'){
					return '<span class="badge badge-warning mb-1 mr-2">Segera</span>';
				}elseif($query->priority=='3'){
					return '<span class="badge badge-danger mb-1 mr-2">Sangat Segera</span>';
				}
			})
			->rawColumns(['priority_color','action'])
            ->make(true);


    }
    public function detail(Request $request, $id){
        $user   = $request->user;
        $sql = "select a.*,
       b.jenis

       from todo a
        left join ref_jenis b on a.kdjenis=b.kdjenis";

        $query = DB::table(DB::raw("($sql) a"))
            ->where('id',$id)
            ->selectRaw('a.*');
        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        $data=$query->first();

        if (empty( $data))
        {
            return "permission denied.";
        }
        $array = [
            'title'     => "Detail $this->title ",
            'url'       => $this->url.'/done/'.$id,
            'back'      => $this->url,
            'form'      =>'<input type="hidden" id="id" name="id_todo" value='.$data->id.'>',
            'input'     => [

                ['title'=> 'Jenis','type' => 'text', 'name' => 'jenis', "value" => $data->jenis,'readonly' => true],
                ['title'=> 'Prioritas','type' => 'textarea', 'name' => 'priority', "value" => $data->priority,'readonly' => true],
                ['title'=> 'Judul','type' => 'text', 'name' => 'title', "value" => $data->title,'readonly' => true],
                ['title'=> 'Deskripsi','type' => 'text', 'name' => 'description', "value" => $data->description,'readonly' => true],
                ['title'=> 'DueDate','type' => 'textarea', 'name' => 'due_date', "value" => $data->due_date,'readonly' => true],

            ],
        ];

        return PublicFunction::view_template($array);
    }
}
?>
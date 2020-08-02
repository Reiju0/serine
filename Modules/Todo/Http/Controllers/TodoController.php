<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use DataTables;
use DB;

class TodoController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
    public function __construct(){
        $this->permission = "Todo.Read";
        $this->table = 'todo';
        $this->kdkey = 'mass_id';
        $this->nmkey = 'id';
        $this->title = 'To DO List';
        $this->url  = url('/todo');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request){
        // dd($request->group);
        $array = [
            'title'  => $this->title,
            'url'    => $this->url.'/data',
            'file_name' => 'daftar To Do',
            'data'   => [
                ['title' => 'Sifat', 'name' => 'priority_color' ],
                ['title' => 'Jenis', 'name' => 'jenis' ],
				['title' => 'Judul', 'name' => 'title' ],
                ['title' => 'Uraian', 'name' => 'description' ],
                ['title' => 'Batas Waktu', 'name' => 'deadline' ],
                ['title' => 'Penerima', 'name' => 'recipient' ],
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
				//['type' => 'date', 'name' => 'due_date'],
				['type' => 'text', 'name' => 'uraian','text' => 'Uraian'],
				['type' => 'text', 'name' => 'kdsatker','text' => 'Kode Satker'],
				['type' => 'text', 'name' => 'nmsatker','text' => 'Nama Satker']
            ],
			'javascript' => 'function hapus(id){
                    alertify.confirm("Anda yakin akan menghapus data ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "GET",
                                url: "'.url('/todo/hapus/').'/"+id,
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("Data Berhasil dihapus");
                                        ajaxLoad("'.url('/todo').'");
                                    }else{
                                        alertify.log(data);
                                    }
                                    $(".loading").hide();
                                    
                                },
                                error: function (xhr, status, error) {
                                    alertify.log(error);
                                    $(".loading").hide();
                                }
                            });
                                
                        }
                    });
                }',
        ];

        $permission = PublicFunction::permission($request, array('Todo.Admin'));
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
		

        $sql  = "SELECT a.*,date_format(due_date,'%d-%m-%Y') deadline,jenis,ifnull(concat(a.kdsatker,CONCAT(' / ',nmsatker)),concat(jumlah,' Satker')) recipient 
		       FROM (
					SELECT COUNT(*) jumlah,
						   if(is_mass=0,kdsatker,0) kdsatker,
							 kdjenis,
							 priority,
							 title,
							 description,
							 due_date,
							 is_mass,
							 mass_id,
							 id
					FROM todo where deleted_at=0 GROUP BY mass_id) a
					LEFT JOIN ref_jenis b ON a.kdjenis=b.kdjenis
					LEFT JOIN ref_satker c ON a.kdsatker=c.kdsatker";

        $query = DB::table(DB::raw("($sql) a"));

        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {


                if ($request->has('kdjenis') && $request->get('kdjenis')!="") {
                    $query->where('kdjenis', $request->get('kdjenis'));
                }
				
				if ($request->has('priority') && $request->get('priority')!="") {
                    $query->where('priority', $request->get('priority'));
                }
				
				if ($request->has('uraian') && $request->get('uraian')!="") {
                    $query->whereRaw('upper(description) like upper (?)',"%{$request->get('uraian')}%");
                }
								
				if ($request->has('nmsatker') && $request->get('nmsatker')!="") {
                    
					$sql1="select a.kdsatker,mass_id,nmsatker from todo a left join ref_satker b on a.kdsatker=b.kdsatker";
					$query1 = DB::table(DB::raw("($sql1) a"))
					          ->whereRaw('upper(nmsatker) like upper (?)',"%{$request->get('nmsatker')}%")->get();
					
					$data_id=array();
					
					foreach($query1 as $id){
						$data_id[]=$id->mass_id;
					}
					
					$query->whereIn('mass_id',$data_id);
                }
				
				if ($request->has('kdsatker') && $request->get('kdsatker')!="") {
                    
					$sql1="select kdsatker,mass_id from todo";
					$query1 = DB::table(DB::raw("($sql1) a"))
					          ->where('kdsatker',$request->get('kdsatker'))->get();
							  //->where('upper(nama) like upper (?)',"%{$request->get('nama')}%")->get();
					
					$data_id=array();
					
					foreach($query1 as $id){
						$data_id[]=$id->mass_id;
					}
					
					$query->whereIn('mass_id',$data_id);
                }

            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
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
            ->addColumn('action', function ($query) use ($request){
                $action = "";
                $tkdkey = $this->kdkey;
                $permission = PublicFunction::permission($request, array('Todo.Admin'));
                if ($permission['response'] == true)
                {
                    $action ="";
					
					if($query->is_mass==1){
						$action = '<a href="javascript:ajaxLoad(\''.url($this->url.'/detil/'.$query->$tkdkey).'\')" class="btn btn-xs btn-info"><i class="fa fa-search-plus"></i></a>';
                    }
					
					$action .= '<a href="javascript:ajaxLoad(\''.url($this->url.'/update/'.$query->$tkdkey).'\')" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i></a>';
					$action .= '<a class="btn btn-xs btn-danger" href="#" onclick="hapus('.$query->id.')"><i class="fa fa-trash"></i></a>';
                }

                return $action;
            })
			->rawColumns(['priority_color','action'])
            ->make(true);
    }
	
	public function index_detil(Request $request,$mass_id){
        // dd($request->group);
        $array = [
            'title'  => $this->title.' Detil',
            'url'    => $this->url.'/detil/data/'.$mass_id,
			'back'   => $this->url,
			'pre_form'   => '<input type="hidden" id="mass_id" value="'.$mass_id.'">',
            'file_name' => 'daftar To Do',
            'data'   => [
                ['title' => 'Kode', 'name' => 'kode' ],
                ['title' => 'Nama Satker', 'name' => 'nmsatker' ],
                ['title' => 'Uraian', 'name' => 'description' ],
                ['title' => 'Batas Waktu', 'name' => 'due_date' ],
				['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'search' => [

				['type' => 'text', 'name' => 'kdsatker','text' => 'Kode Satker'],
				['type' => 'text', 'name' => 'nmsatker','text' => 'Nama Satker']
            ],
			'javascript' => 'function hapus_detil(id){
                    var mass_id=jQuery("#mass_id").val();
					alertify.confirm("Anda yakin akan menghapus data ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "GET",
                                url: "'.url('/todo/hapus_detil/').'/"+id,
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("Data Berhasil dihapus");
                                        ajaxLoad("'.url('/todo/detil').'/"+mass_id);
                                    }else{
                                        alertify.log(data);
                                    }
                                    $(".loading").hide();
                                    
                                },
                                error: function (xhr, status, error) {
                                    alertify.log(error);
                                    $(".loading").hide();
                                }
                            });
                                
                        }
                    });
                }',
        ];
		
		$count_satker=DB::table('ref_satker')
		              ->count();
		
		$count_todo=DB::table('todo')
		              ->where('mass_id',$mass_id)
					  ->whereNull('deleted_at')
		              ->count();
		
		if($count_satker!==$count_todo)
		{
			$permission = PublicFunction::permission($request, array('Ref.admin'));
			if ($permission['response'] == true)
			{
			$addarray  = ['create'    => $this->url.'/detil/create/'.$mass_id];
			$array = (object) array_merge((array) $array, (array) $addarray);
			}
		}
		
        return PublicFunction::datatables_template($array, $request->group);
        //return view('users::read');
    }

    public function data_detil(Request $request,$mass_id)
    {

        $user   = $request->user;
        
        $GLOBALS['nomor'] = 1 + $request->get('start');
		

        $sql  = "select id,
						is_mass,
						mass_id,
		                concat(b.kdbaes1,concat('.',a.kdsatker)) kode,
 		                date_format(due_date,'%d-%m-%Y') due_date,
						deleted_at,
						nmsatker,
						description
				 from todo a left join ref_satker b on a.kdsatker=b.kdsatker 
				 where mass_id='".$mass_id."'";

        $query = DB::table(DB::raw("($sql) a"))
		         ->whereNull('deleted_at');

        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        return DataTables::of($query)
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
			->filter(function ($query) use ($request) {

				if ($request->has('nmsatker') && $request->get('nmsatker')!="") {
                    
					$query->whereRaw('upper(nmsatker) like upper (?)',"%{$request->get('nmsatker')}%");
	
                }
				
				if ($request->has('kdsatker') && $request->get('kdsatker')!="") {
                    
					$query->where('kdsatker',$request->get('kdsatker'));
                
				}

            })
            ->addColumn('action', function ($query) use ($request){
                $action = "";
                $tkdkey = $this->kdkey;
                $permission = PublicFunction::permission($request, array('Todo.Admin'));
                if ($permission['response'] == true)
                {
                    $action ="";
					
					$action .= '<a class="btn btn-xs btn-danger" href="#" onclick="hapus_detil('.$query->id.')"><i class="fa fa-trash"></i></a>';
                }

                return $action;
            })
			->rawColumns(['action'])
            ->make(true);
    }
}

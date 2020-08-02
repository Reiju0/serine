<?php

namespace Modules\Monitoring\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use DataTables;
use DB;

class MonitoringController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
    public function __construct(){
        $this->permission = "Monitoring.ReadKPPN";
        $this->table = 'todo';
        $this->kdkey = 'mass_id';
        $this->nmkey = 'id';
        $this->title = 'Monitoring To Do List';
        $this->url  = url('/monitoring');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function per_satker(Request $request){
        // dd($request->group);
        $array = [
            'title'  => $this->title,
            'url'    => $this->url.'/per_satker/data',
            'file_name' => 'daftar To Do',
            'data'   => [
                ['title' => 'Kode', 'name' => 'kdsatker' ],
                ['title' => 'Nama Satker', 'name' => 'nmsatker' ],
				['title' => 'Sifat', 'name' => 'priority_color' ],
                ['title' => 'Jenis', 'name' => 'jenis' ],
                ['title' => 'Uraian', 'name' => 'description' ],
                ['title' => 'Batas Waktu', 'name' => 'deadline' ],
                ['title' => 'Selesai', 'name' => 'finish' ],
                ['title' => 'Status', 'name' => 'sts_color' ]
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
				['type' => 'select', 'name' => 'counter', 'text' => 'Pilih Counter', 'option' => 
                    [
                        ['val' => '0', 'text' => 'Hari Ini'],
                        ['val' => '1', 'text' => 'On Going'],
                        ['val' => '2', 'text' => 'Expired'],
                        ['val' => '3', 'text' => 'Selesai'],
                    ]
                ],
				['type' => 'text', 'name' => 'kdsatker','text' => 'Kode Satker'],
				['type' => 'text', 'name' => 'nmsatker','text' => 'Nama Satker']
            ]
        ];

        return PublicFunction::datatables_template($array, $request->group);
    }

    public function data_per_satker(Request $request)
    {

        $user   = $request->user;
        
        $GLOBALS['nomor'] = 1 + $request->get('start');
		

        $sql  = "SELECT kdsatker,
						nmsatker,
						priority,
						kdjenis,
						jenis,
						status,
						description,
						date_format(due_date,'%d-%m-%Y') deadline,
						if(done_date=0,'-',date_format(done_date,'%d-%m-%Y')) finish,
						counter,if(STATUS=0,counter,'Selesai') sts FROM (
					SELECT DATEDIFF(due_date,CURDATE()) counter,
						   a.*,
							 nmsatker,
							 jenis 
							 FROM todo a 
					  LEFT JOIN ref_satker b 
					   ON a.kdsatker=b.kdsatker 
					  LEFT JOIN ref_jenis c 
					   ON a.kdjenis=c.kdjenis 
					  WHERE deleted_at=0) a";

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
				
				if ($request->has('counter') && $request->get('counter')!="") {
                    $counter=$request->get('counter');
					if($counter==0){
						$query->where(['counter' => $request->get('counter'),'status' => 0]);
					}elseif($counter==1){
						$query->where('counter','>',0);
						$query->where('status','=',0);
					}elseif($counter==2){
						$query->where('counter', '<',0);
						$query->where('status','=',0);
					}elseif($counter==3){
						$query->where('status',1);
					}
                }
							
				if ($request->has('nmsatker') && $request->get('nmsatker')!="") {
                    
					$query->whereRaw('upper(nmsatker) like upper (?)',"%{$request->get('nmsatker')}%");
                }
				
				if ($request->has('kdsatker') && $request->get('kdsatker')!="") {
                    
					$query->where('kdsatker',$request->get('kdsatker'));
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
			->addColumn('sts_color', function ($query) {
				if($query->counter<'0' && $query->status!==1){
					return '<span class="badge badge-danger mb-1 mr-2">Lewat '.abs($query->counter).' hari</span>';
				}elseif($query->counter=='0' && $query->status!==1){
					return '<span class="badge badge-warning mb-1 mr-2">Hari Ini</span>';
				}elseif($query->status=='1'){
					return '<span class="badge badge-info mb-1 mr-2">Selesai</span>';
				}elseif($query->counter>'0' && $query->status!==1){
					return '<span class="badge badge-primary mb-1 mr-2">'.$query->counter.' hari lagi</span>';
				}
			})
			->rawColumns(['priority_color','sts_color','action'])
            ->make(true);
    }
}
?>

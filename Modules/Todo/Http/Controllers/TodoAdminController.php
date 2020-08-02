<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use PublicRef;
use DataTables;
use Validator;
use DB;
use Carbon\Carbon;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class TodoAdminController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
	
    public function __construct(){
        $this->permission = "Todo.Admin";
        $this->table = 'todo';
        $this->kdkey = 'id';
        $this->nmkey = 'id';
        $this->title = 'To DO List';
        $this->url  = url('/todo');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');
    }
	
	public function create(Request $request){

        $array = [
            'title'     => 'Rekam '.$this->title,
            'url'       => $this->url.'/create',
            'back'      => $this->url,
			
            'input'     => [
                ['type' => 'rowhtml', 'html' => 'Pilih <strong>Semua</strong> pada isian Tujuan untuk mengirimkan kepada semua satker'],
				['title'=> 'Jenis To Do', 'type' => 'template', 'name' => 'getRefTableAdvance', 'param' => 'select', 'option' => 
				['select * from ref_jenis where status=1 order by uid', 'kdjenis','kdjenis', 'jenis', 0, true], 'required' => true],
				['title' => 'Sifat','type' => 'radio-inline', 'name' => 'priority', 'text' => 'Prioritas','required'=>true, 'option' => 
                    [
                        ['val' => '1', 'text' => 'Biasa', 'checked' => '1'],
                        ['val' => '2', 'text' => 'Segera','checked' => '0'],
                        ['val' => '3', 'text' => 'Sangat Segera','checked' => '0'],
                    ]
                ],
				['title'=> 'Tujuan', 'type' => 'template', 'name' => 'getRefTableAdvanceMulti', 'param' => 'select', 'option' => 
				['select * from ref_satker order by kdbaes1,kdsatker', 'kdsatker','kdsatker', 'nmsatker', 0, true], 'required' => true],
				['title'=> 'Perihal','type' => 'text', 'name' => 'perihal', 'required' => true],
				['title'=> 'Uraian','type' => 'textarea', 'name' => 'uraian', 'required' => true],
				['title'=> 'Batas Waktu','type' => 'date', 'name' => 'due_date', 'required' => true]
            ]
        ];

        return PublicFunction::create_template($array);
    }
	
	public function do_create(Request $request){

        $user= Sentinel::check();
		
		$validator = Validator::make($request->all(), [
           'kdjenis'      => 'required',    
           'priority'     => 'required',    
           'perihal'      => 'required',    
           'uraian'       => 'required',    
           'due_date'     => 'required'    
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $kdjenis       = PublicFunction::htmlTag($request->input('kdjenis'));
        $priority      = PublicFunction::htmlTag($request->input('priority'));
        $perihal       = PublicFunction::htmlTag($request->input('perihal'));
        $uraian        = PublicFunction::htmlTag($request->input('uraian'));
		$due_date	   = trim($request->input('due_date'));
        $kdsatker      = $request->input('kdsatker',[]);
		$mass_id       = str_random(12);
		
				
		foreach($kdsatker as $satker){
			
			
			$dt_satker=DB::table('ref_satker');
			
			
			//kirim ke semua satker
			if($satker=='xx'){
				
				$is_mass=1;
				
			}else{
				
				$cek_satker=DB::table('ref_satker')
				            ->whereIn('kdsatker',$kdsatker)
							->count();
							
				if($cek_satker=='1'){
					$is_mass=0;
				}else{
					$is_mass=1;
				}
				
				$dt_satker=$dt_satker->whereIn('kdsatker',$kdsatker);
			}
			
			
			$dt_satker=$dt_satker->get();
			
			$check=0;
				
			foreach ($dt_satker as $x_satker){
				$insert=DB::table($this->table)->insert([
							'kdsatker'       => $x_satker->kdsatker,
							'kdjenis'		 => $kdjenis,
							'priority'		 => $priority,
							'title'			 => $perihal,
							'description'	 => $uraian,
							'due_date'		 => Carbon::createFromFormat('Y-m-d',$due_date),
							'is_mass'		 => $is_mass,
							'mass_id'		 => $mass_id,
							'created_at'	 => Carbon::now(),
							'created_by'	 => $user->id
						]);
						
				if($insert){
					$check=1;
				}
			}
			
			if($check==1){
				return "success";
			}
				
		}
    }
	
	public function create_detil(Request $request,$mass_id){

        $array = [
            'title'     => 'Rekam Tujuan '.$this->title,
            'url'       => $this->url.'/detil/create',
            'back'      => $this->url.'/detil/'.$mass_id,
			
            'input'     => [
                ['type' => 'rowhtml', 'html' => '<input type="hidden" value="'.$mass_id.'" id="mass_id" name="mass_id">'],
				['title'=> 'Tujuan', 'type' => 'template', 'name' => 'getRefTableAdvanceMulti', 'param' => 'select', 'option' => 
				['select * from ref_satker where kdsatker not in (select kdsatker from todo where mass_id="'.$mass_id.'" and deleted_at=0) order by kdbaes1,kdsatker', 'kdsatker','kdsatker', 'nmsatker', 0, true], 'required' => true]
            ]
        ];
        return PublicFunction::create_template($array);
    }
	
	public function do_create_detil(Request $request){

        $user= Sentinel::check();
		
		$validator = Validator::make($request->all(), [
           'mass_id'      => 'required' 
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }
		
		
        $mass_id       = PublicFunction::htmlTag($request->input('mass_id'));
		
		$data=DB::table('todo')
		      ->where('mass_id',$mass_id)
			  ->first();
			  
        $kdjenis       = $data->kdjenis;
        $priority      = $data->priority;
        $perihal       = $data->title;
        $uraian        = $data->description;
		$due_date	   = $data->due_date;
		$kdsatker      = $request->input('kdsatker',[]);
		
				
		foreach($kdsatker as $satker){
			
			$sql  = "select * from ref_satker where kdsatker not in (select kdsatker from todo where mass_id='$mass_id' and deleted_at=0)";

			$dt_satker = DB::table(DB::raw("($sql) a"));
					
			
			//kirim ke semua satker
			if($satker=='xx'){
				
				$is_mass=1;
				
			}else{
				
				$cek_satker=DB::table('ref_satker')
				            ->whereIn('kdsatker',$kdsatker)
							->count();
							
				if($cek_satker=='1'){
					$is_mass=0;
				}else{
					$is_mass=1;
				}
				
				$dt_satker=$dt_satker->whereIn('kdsatker',$kdsatker);
			}
			
			
			$dt_satker=$dt_satker->get();
			
			$check=0;
				
			foreach ($dt_satker as $x_satker){
				$insert=DB::table($this->table)->insert([
							'kdsatker'       => $x_satker->kdsatker,
							'kdjenis'		 => $kdjenis,
							'priority'		 => $priority,
							'title'			 => $perihal,
							'description'	 => $uraian,
							'due_date'		 => Carbon::createFromFormat('Y-m-d',$due_date),
							'is_mass'		 => 1,
							'mass_id'		 => $mass_id,
							'created_at'	 => Carbon::now(),
							'created_by'	 => $user->id
						]);
						
				if($insert){
					$check=1;
				}
			}
			
			if($check==1){
				return "success";
			}
				
		}
    }
	
	public function update(Request $request,$id){
		
        $data=DB::table('todo')
		      ->where('mass_id',$id)
			  ->first();
			  
		if($data->is_mass==1){
			$kdsatker='xx';
			$is_required=false;
		}else{
			$kdsatker=$data->kdsatker;
			$is_required=true;
		}
			  
		$array = [
            'title'     => 'Update '.$this->title,
            'url'       => $this->url.'/update',
            'back'      => $this->url,
			'form'		=> '<input type="hidden" name="mass_id" value="'.$id.'"><input type="hidden" name="is_mass" value="'.$data->is_mass.'">',
			
            'input'     => [
                ['title'=> 'Jenis To Do', 'type' => 'template', 'name' => 'getRefTableAdvance', 'param' => 'select', 'option' => 
				['select * from ref_jenis where status=1 order by kdjenis', 'kdjenis','kdjenis', 'jenis', $data->kdjenis, true], 'required' => true],
				['title' => 'Sifat','type' => 'radio-inline', 'name' => 'priority','value' => $data->priority, 'text' => 'Prioritas','required'=>true, 'option' => 
                    [
                        ['val' => '1', 'text' => 'Biasa' ],
                        ['val' => '2', 'text' => 'Segera'],
                        ['val' => '3', 'text' => 'Sangat Segera'],
                    ]
                ],
				['title'=> 'Tujuan', 'type' => 'template', 'name' => 'getRefTableAdvance', 'param' => 'select', 'option' => 
				['select * from ref_satker order by kdbaes1,kdsatker', 'kdsatker','kdsatker', 'nmsatker', $kdsatker, $is_required]],
				['title'=> 'Perihal','type' => 'text', 'name' => 'perihal', 'value'=>$data->title,'required' => true],
				['title'=> 'Uraian','type' => 'textarea', 'name' => 'uraian','value'=>$data->description, 'required' => true],
				['title'=> 'Batas Waktu','type' => 'date', 'name' => 'due_date', 'value'=>$data->due_date,'required' => true]
            ]
        ];

        return PublicFunction::update_template($array);
    }
	
	public function do_update(Request $request){

        $user= Sentinel::check();
		
		$validator = Validator::make($request->all(), [
           'is_mass'      => 'required',    
           'mass_id'      => 'required',    
           'kdjenis'      => 'required',    
           'priority'     => 'required',    
           'perihal'      => 'required',    
           'uraian'       => 'required',    
           'due_date'     => 'required'    
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $is_mass       = PublicFunction::htmlTag($request->input('is_mass'));
        $mass_id       = PublicFunction::htmlTag($request->input('mass_id'));
        $kdjenis       = PublicFunction::htmlTag($request->input('kdjenis'));
        $priority      = PublicFunction::htmlTag($request->input('priority'));
        $perihal       = PublicFunction::htmlTag($request->input('perihal'));
        $uraian        = PublicFunction::htmlTag($request->input('uraian'));
		$due_date	   = trim($request->input('due_date'));
        $kdsatker      = $request->input('kdsatker');		
		
		$data=DB::table('todo')
		      ->where('mass_id',$mass_id)
			  ->first();
			  
		if($data->is_mass==1){
			$data=[	'kdjenis'		 => $kdjenis,
					'priority'		 => $priority,
					'title'			 => $perihal,
					'description'	 => $uraian,
					'due_date'		 => Carbon::createFromFormat('Y-m-d',$due_date),
					'updated_at'	 => Carbon::now(),
					'updated_by'	 => $user->id];
		}else{
			$data=[	'kdsatker'		 => $kdsatker,
			        'kdjenis'		 => $kdjenis,
					'priority'		 => $priority,
					'title'			 => $perihal,
					'description'	 => $uraian,
					'due_date'		 => Carbon::createFromFormat('Y-m-d',$due_date),
					'updated_at'	 => Carbon::now(),
					'updated_by'	 => $user->id];
		}
		
		$update=DB::table($this->table)
		        ->where('mass_id',$mass_id)
				->update($data);
						
		if($update){
			return "success";
		}
		
    }
	
	public function do_delete(Request $request, $id){
        
		$user=Sentinel::check();
		
		$get_mass=DB::table($this->table)
				  ->where($this->kdkey, '=', $id)
				  ->first();
		
		DB::table($this->table)
				  ->where('mass_id', '=', $get_mass->mass_id)
				  ->update(['deleted_by' => $user->id,'deleted_at' => Carbon::now()]);
		
        
		return "success";
    }
	
	public function do_delete_detil(Request $request, $id){
        
		$user=Sentinel::check();
		
		$get_mass=DB::table($this->table)
				  ->where($this->kdkey, '=', $id)
				  ->update(['deleted_by' => $user->id,'deleted_at' => Carbon::now()]);
				  
		return "success";
    }
}
?>
<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use DB;
use DataTables;
use PublicFunction;
use Validator;
use Carbon\Carbon;

class DashboardSatkerAdminController extends Controller
{
    private $permission;
    private $url;
    private $table;
    private $statusdone;

    private $title;
    private $date;
    public function __construct(){

        $this->user = Sentinel::check();
        $this->date =  Carbon::now('Asia/Jakarta');
        $this->permission = "Dashboard.satker_create";
        $this->table = 'todo';
        $this->title = 'Dashboard Satker';
        $this->statusdone =1;
        $this->url  = url('/dashboard/satker');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }
    public function done(Request $request, $id){

        $user   = $request->user;
        $sql = "select a.*,
       b.jenis

       from todo a
        left join ref_jenis b on a.kdjenis=b.kdjenis";

        $query = DB::table(DB::raw("($sql) a"))
            ->where('id',$id)
            ->whereNull('deleted_at')
            ->selectRaw('a.*');
        $query = PublicFunction::WhereGroupFilter($request->group, $query);

        $data=$query->first();

        if(isset($data)){
            $array = [
                'title'     => "Mark as Done $this->title ",
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
            return PublicFunction::update_template($array);
        }else{
            return "permission denied.";
        }
    }
    public function do_done(Request $request, $id){
        $messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'numeric'   => 'Kolom :attribute harus diisi angka',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai',
            'alpha'     => 'Kolom :attribute harus diisi alpha tanpa space'
        ];


        $validator = Validator::make($request->all(), [

        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $id_todo  = trim($request->input('id_todo'));

        if($id != $id_todo){
            return "id salah";
        }

        try{

            DB::table($this->table)
                ->where('id', '=', $id_todo)
                ->update([
                        'status'       => $this->statusdone,
                        'done_date'     => $this->date,
                    ]
                );
            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        }


    }

}
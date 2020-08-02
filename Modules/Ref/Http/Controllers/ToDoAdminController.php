<?php

namespace Modules\Ref\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use PublicFunction;
use PublicRef;
use DataTables;
use Validator;
use DB;

class ToDoAdminController extends Controller
{

    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
    public function __construct(){
        $this->permission = "Ref.admin";
        $this->table = 'ref_jenis';
        $this->kdkey = 'kdjenis';
        $this->nmkey = 'jenis';
        $this->title = 'Ref Jenis To DO';
        $this->url  = url('/ref/todo');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function create(Request $request){

        $array = [
            'title'     => 'Create '.$this->title,
            'url'       => $this->url.'/create',
            'back'      => $this->url,
            'input'     => [
                ['type' => 'text', 'name' => $this->kdkey, 'required' => true],
                ['type' => 'text', 'name' => $this->nmkey, 'required' => true]
                //['title'=> 'Rumpun', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kdrumpun', 0, true], 'required' => true],
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ]
        ];

        return PublicFunction::create_template($array);
    }

    public function do_create(Request $request){

        $validator = Validator::make($request->all(), [
            $this->kdkey      => 'required',
            $this->nmkey       => 'required'   
            //'kdrumpun'      => 'required|numeric',      
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $nmkey       = PublicFunction::htmlTag($request->input($this->nmkey));
        $kdkey         = PublicFunction::htmlTag($request->input($this->kdkey));
        //$kdrumpun         = PublicFunction::htmlTag($request->input('kdrumpun'));

            
        $check = DB::table($this->table)
            ->where($this->kdkey, '=', $kdkey)
            ->count();

        if($check < 1){
            try{
                DB::table($this->table)->insert([
                        $this->nmkey       => $nmkey,
                        $this->kdkey      => $kdkey,
                        'status'        => '1'
                        ]
                );
            }catch(Exception $e){
                return "error occurred";
            }
        return "success"; 
        }else{
            echo "$this->kdkey Sudah ada.";
        }
    }

    public function update(Request $request, $id){
        $tkdkey = $this->kdkey;
        $tnmkey = $this->nmkey;
         $data = DB::table($this->table)->where($this->kdkey, $id)->first();
        $array = [
            'title'     => "Update $this->title ".$data->$tkdkey,
            'url'       => $this->url.'/update/'.$id,
            'back'      => $this->url,
            'input'     => [
                ['type' => 'text', 'name' => $this->kdkey, 'required' => true, 'value' => $data->$tkdkey, 'readonly' => true],
                ['type' => 'text', 'name' => $this->nmkey, 'required' => true, 'value' => $data->$tnmkey],
                //['title'=> 'Rumpun', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kdrumpun', $data->kdrumpun, true], 'required' => true],
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ]
        ];

        return PublicFunction::update_template($array);
    }

    public function do_update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            $this->kdkey      => 'required',
            $this->nmkey       => 'required'    
            //'kdrumpun'      => 'required|numeric',      
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $nmkey       = PublicFunction::htmlTag($request->input($this->nmkey));
        $kdkey         = PublicFunction::htmlTag($request->input($this->kdkey));
        //$kdrumpun         = PublicFunction::htmlTag($request->input('kdrumpun'));

        if($id != $kdkey){
            return "$this->kdkey salah";
        }
            

            try{
                DB::table($this->table)
                ->where($this->kdkey, '=', $kdkey)
                ->update([
                        $this->nmkey       => $nmkey,     
                        //'kdrumpun'      => $kdrumpun
                        ]
                );
            }catch(Exception $e){
                return "error occurred";
            }
        return "success"; 

    }

    public function enable(Request $request, $id){
        DB::table($this->table)
            ->where($this->kdkey, '=', $id)
            ->update(['status' => 1]);
        return "<a href='javascript:ajaxLoad(\"$this->url\")'>reload</a>
                <script>$(document).ready( function () {
                  ajaxLoad('$this->url');
                });</script>";
    }

    public function disable(Request $request, $id){
        DB::table($this->table)
            ->where($this->kdkey, '=', $id)
            ->update(['status' => 0]);

        return "<a href='javascript:ajaxLoad(\"$this->url\")'>reload</a>
                <script>$(document).ready( function () {
                  ajaxLoad('$this->url');
                });</script>";
    }
}
?>
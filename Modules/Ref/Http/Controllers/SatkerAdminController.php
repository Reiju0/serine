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

class SatkerAdminController extends Controller
{

    private $permission;
    private $url;
    private $table;
    private $kdkey;
    private $nmkey;
    private $title;
    public function __construct(){
        $this->permission = "Ref.admin";
        $this->table = 'ref_satker';
        $this->kdkey = 'kdsatker';
        $this->nmkey = 'nmsatker';
        $this->title = 'Ref SATKER';
        $this->url  = url('/ref/satker');
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
                ['type' => 'text', 'name' => $this->nmkey, 'required' => true],
                ['type' => 'text', 'name' => 'kddept', 'required' => true],
                ['type' => 'text', 'name' => 'kdbaes1', 'required' => true],
                ['type' => 'select', 'title' => 'kode kewenangan', 'name' => 'kdkewenangan', 'option' => [
                    ['val' => 'KP', 'text' => 'Kantor Pusat'],
                    ['val' => 'KD', 'text' => 'Kantor Daerah'],
                    ['val' => 'DS', 'text' => 'Desentralisasi'],
                    ['val' => 'DK', 'text' => 'Dekonsentrasi'],
                    ['val' => 'TP', 'text' => 'Tugas Pembantuan'],
                    ['val' => 'UB', 'text' => 'Urusan Bersama'],
                ]],
                //['title'=> 'Rumpun', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kdrumpun', 0, true], 'required' => true],
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ]
        ];

        return PublicFunction::create_template($array);
    }

    public function do_create(Request $request){

        $validator = Validator::make($request->all(), [
            $this->kdkey      => 'required|digits_between:1,6',
            $this->nmkey       => 'required', 
            'kddept'      => 'required|numeric',      
            'kdbaes1'      => 'required|numeric'    
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
        $kddept         = PublicFunction::htmlTag($request->input('kddept'));
        $kdbaes1         = PublicFunction::htmlTag($request->input('kdbaes1'));
        $kdkewenangan         = PublicFunction::htmlTag($request->input('kdkewenangan'));
        $kdkppn         = '107'; //didefaultkan
        //$kdrumpun         = PublicFunction::htmlTag($request->input('kdrumpun'));

            
        $check = DB::table($this->table)
            ->where($this->kdkey, '=', $kdkey)
            ->count();

        if($check < 1){
            try{
                DB::table($this->table)->insert([
                        $this->nmkey       => $nmkey,
                        $this->kdkey      => $kdkey,
                        'kddept'      => $kddept,      
                        'kdbaes1'      => $kdbaes1,      
                        'kdkppn'      => $kdkppn,   
                        'kdkewenangan' => $kdkewenangan,   
                        //'kdrumpun'      => $kdrumpun,
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
                ['type' => 'text', 'name' => 'kddept', 'required' => true, 'value' => $data->kddept],
                ['type' => 'text', 'name' => 'kdbaes1', 'required' => true, 'value' => $data->kdbaes1],
                ['type' => 'select', 'title' => 'kode kewenangan', 'name' => 'kdkewenangan', 'value' => $data->kdkewenangan, 'option' => [
                    ['val' => 'KP', 'text' => 'Kantor Pusat'],
                    ['val' => 'KD', 'text' => 'Kantor Daerah'],
                    ['val' => 'DS', 'text' => 'Desentralisasi'],
                    ['val' => 'DK', 'text' => 'Dekonsentrasi'],
                    ['val' => 'TP', 'text' => 'Tugas Pembantuan'],
                    ['val' => 'UB', 'text' => 'Urusan Bersama'],
                ]],
                //['title'=> 'Rumpun', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kdrumpun', $data->kdrumpun, true], 'required' => true],
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ]
        ];

        return PublicFunction::update_template($array);
    }

    public function do_update(Request $request, $id){

        $validator = Validator::make($request->all(), [
            $this->kdkey      => 'required|digits_between:1,6',
            $this->nmkey       => 'required', 
            'kddept'      => 'required|numeric',      
            'kdbaes1'      => 'required|numeric'    
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
        $kddept         = PublicFunction::htmlTag($request->input('kddept'));
        $kdbaes1         = PublicFunction::htmlTag($request->input('kdbaes1'));
        $kdkewenangan         = PublicFunction::htmlTag($request->input('kdkewenangan'));
        $kdkppn         = '107';
        //$kdrumpun         = PublicFunction::htmlTag($request->input('kdrumpun'));

        if($id != $kdkey){
            return "$this->kdkey salah";
        }
            

            try{
                DB::table($this->table)
                ->where($this->kdkey, '=', $kdkey)
                ->update([
                        $this->nmkey       => $nmkey,
                        'kddept'      => $kddept,      
                        'kdbaes1'      => $kdbaes1,      
                        'kdkppn'      => $kdkppn,   
                        'kdkewenangan' => $kdkewenangan   
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
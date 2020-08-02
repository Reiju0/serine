<?php

namespace App\Http\Controllers\Option;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Datatables;
use Validator;
use PublicFunction;

class ReadController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.option_admin');
        $this->middleware('ajax');

    }

    public function init(){
        $seeder = DB::table('options')->where('keys', 'seeder')->first();
        if(is_null($seeder)){
            DB::table('options')->insert([
                'keys'  => 'seeder',
                'val' => 'http://10.242.231.155:8181', 
                'created_at' => \Carbon\Carbon::now()
            ]);
        }

        $sv = DB::table('options')->where('keys', 'sv')->first();
        if(is_null($sv)){
            DB::table('options')->insert([
                'keys'  => 'sv',
                'val' => '1.1.1', 
                'created_at' => \Carbon\Carbon::now()
            ]);
        }
        return "nothing";
    }

    public function index(){
    	$maintenance = DB::table('options')->where('keys', 'maintenance')->first();
    	$message = DB::table('options')->where('keys', 'message')->first();
        $version = DB::table('options')->where('keys', 'version')->first();
        $token = DB::table('options')->where('keys', 'token')->first();
        $seeder = DB::table('options')->where('keys', 'seeder')->first();
    	return view('option/option',['maintenance' => $maintenance, 'message' => $message, 'version' => $version, 'token' => $token, 'seeder' => $seeder]);
    }

    public function update($key, Request $request){
    	$messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai',
            'numeric'	=> 'Kolom :attribute harus angka'
        ];

        $validator = Validator::make($request->all(), [
            'maintenance'     	=> 'numeric'     
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $value   = PublicFunction::htmlTag(trim($request->input($key)));
        $query = DB::table('options')
                    ->where('keys', $key);

        try{

            $query->update([
                    'val'         => $value,
                    'updated_at'    => \Carbon\Carbon::now('Asia/Jakarta')
                    ]);

            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}

<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use PublicFunction;

class UpdateController extends Controller
{
    //
    public function __construct(){
        $this->middleware('permission:Core.admin,Core.menu_admin');
        $this->middleware('ajax');

    }

    public function index($id)
    {
        $query = DB::table('menu')
        	->where('menu.id', '=', $id)
        	->first();

        $permission = explode(",",$query->permission);

        $all = DB::table('permission')->orderBy('permission', 'asc')->get();

        return view('menu/update',['item' => $query, 'all' => $all, 'no' => 0, 'permission'=>$permission]);
    }

    public function update(Request $request)
    {
    	# code...
    	$messages = [
            'required'  => 'Kolom :attribute harus diisi.',
            'mimes'     => 'Tipe file untuk :attribute tidak sesuai'
        ];

        $validator = Validator::make($request->all(), [
            'menu'   => 'required',
            'have_link' => 'required|numeric',
            'path'      => 'required',
            'is_parent'   => 'required|numeric',
            'parent_id'   => 'required',
            'status'   => 'required|numeric'   
        ], $messages);

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $id         = $request->input('id');
        $menu       = PublicFunction::htmlTag($request->input('menu'));
        $have_link  = PublicFunction::htmlTag($request->input('have_link'));
        $path       = PublicFunction::htmlTag($request->input('path'));
        $is_parent  = PublicFunction::htmlTag($request->input('is_parent'));
        $parent_id  = PublicFunction::htmlTag($request->input('parent_id'));
        $status     = PublicFunction::htmlTag($request->input('status'));
        $style      = PublicFunction::htmlTag($request->input('style'));
        $delta      = PublicFunction::htmlTag($request->input('delta'));
        $permission = $request->input('permission');

        $array = '';
        if(!(empty($permission))){
            foreach ($permission as $key) {
                # code...
                if(isset($key['value']) == 1){
                    $array .= $key['name'].',';
                }
            }
        }
        $array .= 'admin';


        $query = DB::table('menu')
                    ->where('id', $id);

        try{

            $query->update([
                    'menu'          => $menu,
                    'have_link'     => $have_link,
                    'path'          => $path,
                    'is_parent'     => $is_parent,
                    'parent_id'     => $parent_id,
                    'status'        => $status,
                    'style'         => $style,
                    'delta'         => $delta,
                    'permission'    => $array
                    ]);

            echo "success";
        }catch(Exception $e){
            echo $e->getMessage();
        }

        
    }
}

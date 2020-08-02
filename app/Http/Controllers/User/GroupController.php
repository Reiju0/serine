<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PublicFunction;
use Validator;
use DataTables;
use PublicRef;
use DB;

class GroupController extends Controller
{
    private $permission;
    private $url;
    public function __construct(){
        $this->url  = url('/user/role/group');
        $this->middleware("permission:Core.profile");
        $this->middleware('ajax');

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $role_id)
    {

        $user = Sentinel::check();

        $array = [
            'title'  => 'Group User '.$user->username,
            'url'    => $this->url.'/data/'.$role_id,
            'file_name' => 'Role '.$user->username,
            'create'    => $this->url.'/add/'.$role_id,
            'back'    => url('/user/role'),
            'data'   => [
                ['title' => 'Kode', 'name' => 'akode'],
                ['title' => 'Uraian', 'name' => 'auraian'],
                ['title' => 'Status', 'name' => 'statusa'],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'javascript' => '
                    function konfirmasi_delete(url){
                        alertify.confirm("Anda yakin akan Menghapus Group User ini?", function (e) {
                            if (e) {
                                ajaxLoad(url);
                            }
                        });
                    }
            '
        ];

        return PublicFunction::datatables_template($array, $request->group);

    }

    
    public function data(Request $request, $role_id){

        $user = Sentinel::check();

        $GLOBALS['nomor'] = 1 + $request->get('start');
        $group  = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->first();
        $data   = json_decode($group->user_groups);
        $ug     = DB::table('user_group ')->where('id', $data->all[0]->id)->first();

        $kode   = $ug->kolom;
        $nama   = $ug->nama;
        $tabel  = $ug->ref_table;

        $query = DB::table($tabel)
                ->whereIn($kode, $data->all[0]->val);

        if(!empty($data->all[0]->val) and !empty($data->off[0]->val)){

        $sql = "SELECT a.*, '1' statusa from $tabel a where $kode in ?
                union all 
                SELECT a.*, '2' statusa from $tabel a where $kode in ?";
        $query = DB::table(DB::raw("($sql) a"))
                ->setBindings([$data->all[0]->val, $data->off[0]->val]);
        }elseif(!empty($data->off[0]->val)){
            $sql = "SELECT a.*, '2' statusa from $tabel a where $kode in ?"; 
            $query = DB::table(DB::raw("($sql) a"))
                ->setBindings([ $data->off[0]->val]);
        }else{
            $sql = "SELECT a.*, '1' statusa from $tabel a where $kode in ?"; 
            $query = DB::table(DB::raw("($sql) a"))
                ->setBindings([$data->all[0]->val]);
        }

        

        return DataTables::of($query)
            ->addColumn('akode', function($query) use ($kode){
                $return = $query->$kode;
                
                return $return;
            })
            ->addColumn('auraian', function($query) use ($nama){
                $return = $query->$nama;
                
                return $return;
            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('statusa', function ($query){
                $return = "Aktif";
                if($query->statusa == '2'){
                    $return = "Tidak Aktif";
                }

                return $return;
            })
            ->addColumn('action', function ($query) use ($role_id, $kode){
                $action = '';
                $action .= '<a href="javascript:konfirmasi_delete(\''.url('/users/role/group/delete/'.$role_id.'/'.$query->$kode).'\')" class="btn btn-xs btn-danger"><i class="fa fa-eraser"></i> delete</a>';
                /*if (app(PublicF::class)->permissionOnly(array('Users.admin_multi'))){
                    $action = '<a href="javascript:ajaxLoad(\''.url('/users/update_multi/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
                }else{*/
                
                return $action;
            })
            ->rawColumns(['kelompok', 'action'])
            ->make(true);
    }

    public function create(Request $request, $role_id){
        
        $user = Sentinel::check();
        $group  = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->first();
        $data   = json_decode($group->user_groups);

        $array = [
            'title'     => 'Add Group Role To User '.$user->username,
            'url'       => $this->url.'/create/'.$role_id,
            'back'      => $this->url.'/'.$role_id,
            'input'     => [
                ['type' => 'template', 'name' => 'kode', 'param' => 'select', 'option' => [$request->group, $data->all[0]->id, 0]]
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ]
        ];

        return PublicFunction::create_template($array);
    }

    public function do_create(Request $request, $role_id){
        $validator = Validator::make($request->all(), [
            'kode'   => 'required'
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }

        $user = Sentinel::check();

        $group  = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->first();
        $data   = json_decode($group->user_groups);

        $kode         = PublicFunction::htmlTag($request->input('kode'));


        $role       = Sentinel::findRoleById($role_id);
        $group_id   = $role->group_id;

        if(isset($group_id) and $group_id > 0){
            $user_group = DB::table('user_group')
                            ->where('id', '=', $group_id)
                            ->first();

            $sql = $user_group->ref_table;

            if($user_group->kolom == 'kdsatker'){
                $sql = " select a.*, b.kdkanwil 
                    from $user_group->ref_table a 
                    left join t_kppn b on a.kdkppn = b.kdkppn
                    ";
            }

            $ref_table = DB::table(DB::raw("($sql) a"))->where("a.$user_group->kolom", $kode);

            //$ref_table = app(PublicF::class)->WhereGroup($ref_table);
            $ref_table = PublicFunction::WhereGroup($request->group, $ref_table);

            if($ref_table->count() < 1){
                return "you don't have permission";
            }
        }

        
        if(!in_array($kode, $data->all[0]->val)){
            array_push($data->all[0]->val, $kode);
        }

        DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->update([
            'user_groups'   => json_encode($data)
        ]);

        $data = [
                'group_id'      => 0,
                'group_value'   => 0,
                'user_group'    => 0
            ];
        DB::table('users')->where('id', $user->id)->update($data);
        //dd(json_encode($data));
        return 'success';
    }

    public function delete(Request $request, $role_id, $kode){
        $user = Sentinel::check();
        $group  = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->first();
        $data   = json_decode($group->user_groups);

        $validation = PublicFunction::UserValidation($request, $user->id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        //$kode         = PublicFunction::htmlTag($request->input('kode'));
        $new_val        = array();
        if(in_array($kode, $data->all[0]->val)){
            if (($key = array_search($kode, $data->all[0]->val)) !== false) {
                unset($data->all[0]->val[$key]);
            }
            //array_push($data->all[0]->val, $kode);
        }

        foreach ($data->all[0]->val as $key) {
            # code...
            array_push($new_val, $key);
        }

        $data->all[0]->val = $new_val;
        //dd($data->all[0]->val);
        $json = json_encode($data);
        //dd($json);

        DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id)->update([
            'user_groups'   => $json
        ]);

        return $this->index($request, $role_id);
    }

    
}

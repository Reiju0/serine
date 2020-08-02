<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PublicFunction;
use DataTables;
use Validator;
use PublicRef;
use DB;

class RoleController extends Controller
{
    private $permission;
    private $url;
    private $roleJafung;
    public function __construct(){
        $this->url  = url('/user/role');
        $this->middleware("permission:Core.profile");
        $this->middleware('ajax');
        $this->roleJafung = ['2', '3', '4', '5'];

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {

        $user = Sentinel::check();
        $array = [
            'title'  => 'Role User '.$user->username,
            'url'    => $this->url.'/data',
            'file_name' => 'Role '.$user->username,
            'create'    => $this->url.'/add',
            'data'   => [
                ['title' => 'Role', 'name' => 'name' ],
                ['title' => 'Group', 'name' => 'kelompok'],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'javascript' => '
                    function konfirmasi_role_delete(url){
                        alertify.confirm("Anda yakin akan Menghapus Role User ini?", function (e) {
                            if (e) {
                                ajaxLoad(url);
                            }
                        });
                    }
            '
        ];

        return PublicFunction::datatables_template($array, $request->group);

    }

    
    public function data(Request $request){
        $GLOBALS['nomor'] = 1 + $request->get('start');
        $user = Sentinel::check();
        $query = DB::table('role_users')->leftjoin('users',  'users.id', '=', 'role_users.user_id')
                ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
                ->select(['role_users.*', 'users.username', 'roles.name', 'roles.group_id'])
                //->where('roles.group_id', '<>', '0')
                ->whereIn('roles.id', $this->roleJafung)
                ->whereNotNull('roles.group_id')
                ->where('user_id', $user->id);

        return DataTables::of($query)
            ->addColumn('kelompok', function($query){
                $return = '';
                $group = json_decode($query->user_groups);
                if(isset($group->all) or isset($group->off)){
                    if(isset($group->all)){
                        $return .= 'Aktif: ';
                        foreach ($group->all as $key) {
                            $val = json_decode(json_encode($key->val), true);
                            if(in_array(0, $val )){
                            }else{
                                $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                                $datas = DB::table($user_group->ref_table)->whereIn($user_group->kolom, $val);
                                $etc    = '';
                                if($datas->count() > 5){
                                   $etc     = '<li> Etc...</li>';
                                }
                                $datas = $datas->limit(5)->get();
                                //dd($datas);
                                $kolom = $user_group->kolom;
                                $nama = $user_group->nama;
                                foreach ($datas as $data) {                             
                                    $return .= "<li>".$data->$kolom." - ".$data->$nama."</li>";
                                }
                            }
                        }
                    }

                    if(isset($group->off)){
                        $return .= 'Tidak Aktif: ';
                        foreach ($group->off as $key) {
                            $val = json_decode(json_encode($key->val), true);
                            if(in_array(0, $val )){
                            }else{
                                $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                                $datas = DB::table($user_group->ref_table)->whereIn($user_group->kolom, $val);
                                $etc    = '';
                                if($datas->count() > 5){
                                   $etc     = '<li> Etc...</li>';
                                }
                                $datas = $datas->limit(5)->get();
                                $kolom = $user_group->kolom;
                                $nama = $user_group->nama;
                                foreach ($datas as $data) {                             
                                    $return .= "<li>".$data->$kolom." - ".$data->$nama."</li>";
                                }
                            }
                        }
                    }
                }else{
                    $return = 'all';
                }
                return PublicFunction::htmlTag($return);
            })
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query){
                $action = '';
                $group = json_decode($query->user_groups);
                if(isset($group->off)){
                    if(count($group->off[0]->val) > 0){
                        $action .= '<a href="javascript:konfirmasi_role_delete(\''.url('/user/role/delete/'.$query->role_id).'\')" class="btn btn-xs btn-danger"><i class="fa fa-eraser"></i> Batalkan</a>';

                    }
                }
                
                return $action;
            })
            ->rawColumns(['kelompok', 'action'])
            ->make(true);
    }

    public function create(Request $request){
        $user = Sentinel::check();

        $roles = DB::table('roles')->where('group_id', '1')->get();
        $role_array = "";
        foreach ($roles as $key) {
            # code...
            $role_array .= '"'.$key->id.'", ';
        }
        $role_array .= '"0"';


        $array = [
            'title'     => 'Add Role To User '.$user->username,
            'url'       => $this->url.'/create',
            'back'      => $this->url,
            'input'     => [
                ['type' => 'template', 'name' => 'role_group', 'param' => 'select'],
                ['title'=> 'KL', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kddept'], 'div_class' => 'div_kl'],
                ['type' => 'template', 'name' => 'kode', 'param' => 'select', 'option' => [$request->group, 0, 0, 'z', false]]
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ],
            'javascript' => '
                var role_array = ['.$role_array.'];
            ',
            'jquery'    => 'jQuery("#role_group").on("change", function(e){
                                var role = $("#role_group").val();
                                var kddept = $("#kddept").val();
                                if(role_array.indexOf(role) != -1){
                                        $(".div_kl").show();
                                    }else{
                                        $(".div_kl").hide();
                                    }
                                $(".loading").show();
                                $.ajax({
                                    url:"'.$this->url.'/kode/"+role+"/"+kddept+"/0",
                                    method: "GET",
                                    success:function(result){
                                        if(result=="none"){
                                            $("#kode").html("<option value=\'0\'></option>");
                                        }else{
                                            $("#kode").html(result).trigger("chosen:updated");
                                        }
                                        $(".loading").hide();
                                    }
                                });
                            });
                            
                            $(\'select[name="role_group"]\').trigger("chosen:updated").change();
                            $("#kddept").on("change", function(e){
                                $(\'select[name="role_group"]\').trigger("chosen:updated").change();
                            });
                            
            '
        ];

        return PublicFunction::create_template($array);
    }

    public function do_create(Request $request){
        $validator = Validator::make($request->all(), [
            'role'  => 'numeric'
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
        }


        $role_id    = PublicFunction::htmlTag($request->input('role_group'));
        $kode       = PublicFunction::htmlTag($request->input('kode'));

        $role       = Sentinel::findRoleById($role_id);
        $group_id   = $role->group_id;

        if(!in_array($role_id, $this->roleJafung)){
            return "Can't add this role form this menu";
        }

        $group = '0';

        if($group_id > 0){
            $group = array();
            $group['off'][] = array( 'id' => $group_id, 'val'=> array($kode));
            $group = json_encode($group);
        }

        $user = Sentinel::check();

        $query = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id);

        $count = $query->count();

        $data = [
                'group_id'      => 0,
                'group_value'   => 0,
                'user_group'    => 0
            ];


        if($count > 0){
            //return "User sudah mempunyai level ini";
            $current = $query->first();
            $data   = json_decode($current->user_groups);

            /*if(isset($data->off)){
                if(!in_array($kode, $data->off[0]->val)){
                    array_push($data->off[0]->val, $kode);
                }
            }else{*/
                $add_on = false;
                if(isset($data->all)){
                    $new_data = array();
                    $new_data = ['id' => $role->group_id, 'val' => $data->all[0]->val];
                    $add_on = true;
                }
                $data = array();
                $data['off'][] = ['id' => $role->group_id, 'val' => [$kode]];
                if($add_on){
                    $data['all'][] = $new_data;
                }   
            //}

            $query->update([
                    'user_groups' => json_encode($data)
                ]);
            
        }else{

            $role->users()->attach($user);
            DB::table('role_users')
                ->where('user_id', $user->id)
                ->where('role_id', $role_id)
                ->update([
                    'user_groups' => $group
                ]);
        }
        

        return 'success';

    }

    public function getKode(Request $request, $role_id, $kddept, $selected){
        $role = DB::table('roles')->where('id', $role_id)->first();

        $return = PublicRef::kode($request->group, $role->group_id, $selected, $kddept, false);
        return $return;
    }

    public function update (Request $request, $rid){

        $user = Sentinel::check();

        $array = [
            'title'  => 'Edit Role '.$rid.' User '.$user->username,
            'url'    => $this->url.'/update/'.$rid.'/data',
            'file_name' => 'Role '.$user->username,
            'create'    => $this->url.'/add',
            'back'    => url('/users'),
            'data'   => [
                ['title' => 'Role', 'name' => 'name' ],
                ['title' => 'Group', 'name' => 'kelompok'],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ]
        ];

        return PublicFunction::datatables_template($array, $request->group);
    }

    public function delete_role(Request $request, $role_id){
        $user   = Sentinel::check();

        $query  = DB::table('role_users')->where('user_id', $user->id)->where('role_id', $role_id);

        $count   = $query->count();
        $new_data   = array();

        if($count > 0){
            $current = $query->first();
            $data   = json_decode($current->user_groups);

            if(isset($data->all)){
                $new_data['all'] = $data->all;

                $query->update([
                    'user_groups' => json_encode($new_data)
                ]);
            }else{
                $query->delete();
            }
        }

        return $this->index($request);
    }
}

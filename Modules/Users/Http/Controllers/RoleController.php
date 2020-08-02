<?php

namespace Modules\Users\Http\Controllers;

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
        $this->permission = "Core.admin";
        $this->url  = url('/users/role');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');
        $this->roleJafung = ['2', '3', '4', '5'];

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request, $id)
    {
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        $user = Sentinel::findById($id);

        $array = [
            'title'  => 'Role User '.$user->username,
            'url'    => $this->url.'/data/'.$id,
            'file_name' => 'Role '.$user->username,
            'create'    => $this->url.'/add/'.$id,
            'back'    => url('/users'),
            'data'   => [
                ['title' => 'Role', 'name' => 'name' ],
                ['title' => 'Type', 'name' => 'kantor'],
                // ['title' => 'Kode', 'name' => 'val'],
                ['title' => 'Nama', 'name' => 'kelompok'],
                ['title' => 'Status', 'name' => 'sts_text'],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'javascript' => 'function data_delete(userId, roleId, nilai){
                    alertify.confirm("Anda yakin akan menghapus role user ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "POST",
                                url: "'.url('/users/delete').'/"+userId+"/"+roleId+"/"+nilai+"?_token='.csrf_token().'",
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("role berhasil dihapus");
                                        ajaxLoad("'.url('/users/role/'.$id).'");
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
                }
                function data_enable(userId, roleId, nilai){
                    alertify.confirm("Anda yakin akan mengaktifkan role user ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "POST",
                                url: "'.url("/users/enable").'/"+userId+"/"+roleId+"/"+nilai+"?_token='.csrf_token().'",
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("role berhasil diaktifkan");
                                        ajaxLoad("'.url('/users/role/'.$id).'");
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
                }
            ',
        ];

        return PublicFunction::datatables_template($array, $request->group);

    }

    
    public function data(Request $request, $id){
        $GLOBALS['nomor'] = 1 + $request->get('start');

        $user   = Sentinel::check();
        $role   = Sentinel::findById($user->id)->roles;
        $admin_of = [];
        //dd($role);

        foreach ($role as $key) {
            # code...
            if(!empty($key->admin_of)){
                $array = json_decode($key->admin_of, true);
                foreach ($array as $keys) {
                    # code...
                    $admin_of[] = $keys;
                }
            } 
        }

        $sql = "
            SELECT a.*, h.name, CASE a.id 
                    WHEN '0' THEN 'Not Defined' 
                    WHEN '1' THEN b.nmsatker 
                    WHEN '2' THEN c.nmunit 
                    WHEN '3' THEN d.nmdept 
                    WHEN '4' THEN e.nmkppn 
                    WHEN '5' THEN f.nmkanwil 
                    WHEN '6' THEN g.nmkantor 
                    END AS kelompok,
                CASE a.id 
                    WHEN '0' THEN 'ALL' 
                    WHEN '1' THEN 'SATKER' 
                    WHEN '2' THEN 'ES1' 
                    WHEN '3' THEN 'DEPT' 
                    WHEN '4' THEN 'KPPN' 
                    WHEN '5' THEN 'KANWIL' 
                    WHEN '6' THEN 'INTERNAL' 
                    END AS kantor,
                case a.sts_role
                    WHEN '0' THEN ' Tidak Aktif'
                    WHEN '1' THEN ' Aktif'
                    END AS sts_text,
                CASE a.id 
                    WHEN '1' THEN b.KDKPPN 
                    WHEN '4' THEN e.KDKPPN 
                    WHEN '6' THEN g.KDKPPN 
                    END AS kdkppn,
                CASE a.id 
                    WHEN '5' THEN f.KDKANWIL 
                    END AS kdkanwil,
                CASE a.id WHEN '1' THEN b.kdsatker END AS kdsatker,
                CASE a.id WHEN '2' THEN c.kdbaes1 END AS kdbaes1,
                CASE a.id WHEN '3' THEN d.KDDEPT END AS kddept,
                CASE a.id WHEN '6' THEN g.kdkantor END AS kdkantor
            FROM (
                SELECT a.user_id, a.role_ID, 
                    CASE WHEN b.id IS NULL THEN '0' ELSE b.id END AS id,
                    CASE WHEN b.val IS NULL THEN 'Not Defined' ELSE b.val END AS val,
                    CASE WHEN b.sts_role IS NULL THEN '1' ELSE b.sts_role END AS sts_role
                from role_users a 
                left join (
                    SELECT * from (
                        SELECT USER_ID, ROLE_ID, id, val, '1' sts_role
                        FROM ROLE_USERS, 
                            json_table(USER_GROUPS, '$' 
                            columns(
                                id VARCHAR2(20) PATH '$.all.id',
                                NESTED PATH '$.all.val[*]' columns (
                                    val PATH '$'
                                )
                            )
                        ) WHERE val is not null
                    ) union all (
                        SELECT USER_ID, ROLE_ID, id, val, '0' sts_role
                        FROM ROLE_USERS, 
                            json_table(USER_GROUPS, '$' 
                            columns(
                                id VARCHAR2(20) PATH '$.off.id',
                                NESTED PATH '$.off.val[*]' columns (
                                    val PATH '$'
                                )
                            )
                        ) WHERE val is not null
                    )
                ) b on a.user_id = b.user_id and a.role_id = b.role_id
            ) a
            LEFT JOIN t_satker b ON a.val = b.KDSATKER
            LEFT JOIN t_unit c ON a.val = c.KDBAES1
            LEFT JOIN T_DEPT d ON a.val = d.KDDEPT
            LEFT JOIN T_KPPN e ON a.val = e.KDKPPN
            LEFT JOIN T_KANWIL f ON a.val = f.KDKANWIL
            LEFT JOIN t_kantor g ON a.val = g.kdkantor
            LEFT JOIN roles h ON a.role_id = h.id
        ";

        $query = DB::table(DB::raw("($sql) a"))
                ->where('user_id', $id);

        if($request->group != 'all'){
            $query = $query->wherein('role_id', $admin_of);
            $query = PublicFunction::WhereGroup($request->group, $query);
        }

        return DataTables::of($query)
            
            ->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query) use ($id){
                $action = '';
                /*if($query->group_id > 0){
                    $action .= '<a href="javascript:ajaxLoad(\''.url('/users/role/group/'.$id.'/'.$query->role_id).'\')" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i> Group</a>';
                }
                $action .= '<a href="javascript:konfirmasi_role_delete(\''.url('/users/role/delete/'.$id.'/'.$query->role_id).'\')" class="btn btn-xs btn-danger"><i class="fa fa-eraser"></i> delete</a>';
                /*if (app(PublicF::class)->permissionOnly(array('Users.admin_multi'))){
                    $action = '<a href="javascript:ajaxLoad(\''.url('/users/update_multi/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
                }else{*/
                $action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->user_id.', '.$query->role_id.', \''.$query->val.'\')"><i class="fa fa-trash"></i> Delete</a>';
                if($query->sts_role == '0'){
                    $action .= '<a class="btn btn-xs btn-info" id="'.$query->id.'" onclick="data_enable('.$query->user_id.', '.$query->role_id.', \''.$query->val.'\')"><i class="fa fa-trash"></i> Enable</a>';
                }
                
                return $action;
            })
            ->rawColumns(['kelompok', 'action'])
            ->make(true);
    }

    public function create(Request $request, $id){
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        $user = Sentinel::findById($id);

        $roles = DB::table('roles')->where('group_id', '1')->get();
        $role_array = "";
        foreach ($roles as $key) {
            # code...
            $role_array .= '"'.$key->id.'", ';
        }
        $role_array .= '"0"';

        $array = [
            'title'     => 'Add Role To User '.$user->username,
            'url'       => $this->url.'/create/'.$id,
            'back'      => $this->url.'/'.$id,
            'input'     => [
                ['type' => 'template', 'name' => 'role', 'param' => 'select'],
                ['title'=> 'KL', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kddept'], 'div_class' => 'div_kl'],
                ['type' => 'template', 'name' => 'kode', 'param' => 'select', 'option' => [$request->group, 0, 0]]
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ],
            'javascript' => '
                var role_array = ['.$role_array.'];
            ',
            'jquery'    => 'jQuery("#role").on("change", function(e){
                                var role = $("#role").val();
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
                            $(\'select[name="role"]\').trigger("chosen:updated").change();
                            $("#kddept").on("change", function(e){
                                $(\'select[name="role"]\').trigger("chosen:updated").change();
                            });
            '
        ];

        return PublicFunction::create_template($array);
    }

    public function do_create(Request $request, $id){
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

        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        $user   = Sentinel::check();
        $role   = Sentinel::findById($user->id)->roles;
        $admin_of = [];
        //dd($role);

        foreach ($role as $key) {
            # code...
            if(!empty($key->admin_of)){
                $array = json_decode($key->admin_of, true);
                foreach ($array as $keys) {
                    # code...
                    $admin_of[] = $keys;
                }
            } 
        }


        $role_id    = PublicFunction::htmlTag($request->input('role'));
        $kode       = PublicFunction::htmlTag($request->input('kode'));

        $role       = Sentinel::findRoleById($role_id);
        $group_id   = $role->group_id;

        $group = '0';

        if($group_id > 0){
            $group = array();
            $group['all'][] = array( 'id' => $group_id, 'val'=> array($kode));
            $group = json_encode($group);
        }

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

        $permission = PublicFunction::permission($request, ['superadmin']);
        if(!in_array($role_id, $admin_of) and $permission['response'] == false){
            return "you don't have permission for this role";
        }

        $query = DB::table('role_users')->where('user_id', $id)->where('role_id', $role_id);

        $count = $query->count();

        $user = Sentinel::findById($id);

        if($count > 0 and (!in_array($role_id, $this->roleJafung))){
            //return "User sudah mempunyai level ini";
            $current = $query->first();
            $data   = json_decode($current->user_groups);

            /*cek data on active group*/
            if(isset($data->all)){
                if(!in_array($kode, $data->all[0]->val)){
                    array_push($data->all[0]->val, $kode);
                }
            }else{
                $add_off = false;
                if(isset($data->off)){
                    $new_data = array();
                    $new_data = ['id' => $role->group_id, 'val' => $data->off[0]->val];
                    $add_off = true;
                }
                $data = array();
                $data['all'][] = ['id' => $role->group_id, 'val' => [$kode]];
                if($add_off){
                    $data['off'][] = $new_data;
                }
                
            }

            $query->update([
                    'user_groups' => json_encode($data)
                ]);
            
        }else{
            $query->delete();
            $role->users()->attach($user);
            $query->update([
                    'user_groups' => $group
                ]);

            
        }

        if(in_array($role_id, $this->roleJafung)){
            $sql = DB::table('role_users')
                ->where('user_id', $user->id)
                ->where('role_id', '<>', $role_id)
                ->whereIn('role_id', $this->roleJafung)
                ->delete();
        }


        return 'success';

    }

    public function getKode(Request $request, $role_id, $kddept, $selected){
        $role = DB::table('roles')->where('id', $role_id)->first();

        $return = PublicRef::kode($request->group, $role->group_id, $selected, $kddept);
        return $return;
    }

    public function update (Request $request, $id, $rid){
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        $user = Sentinel::findById($id);

        $array = [
            'title'  => 'Edit Role '.$rid.' User '.$user->username,
            'url'    => $this->url.'/update/'.$id.'/'.$rid.'/data',
            'file_name' => 'Role '.$user->username,
            'create'    => $this->url.'/add/'.$id,
            'back'    => url('/users'),
            'data'   => [
                ['title' => 'Role', 'name' => 'name' ],
                ['title' => 'Group', 'name' => 'kelompok'],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ]
        ];

        return PublicFunction::datatables_template($array, $request->group);
    }

    public function delete_role(Request $request, $id, $role_id){
        $validation = PublicFunction::UserValidation($request, $id, $this->permission);
        if($validation == false){
            return "cannot change this user information";
        }

        DB::table('role_users')
            ->where('user_id', $id)
            ->where('role_id', $role_id)
            ->delete();
        return $this->index($request, $id);
    }
}

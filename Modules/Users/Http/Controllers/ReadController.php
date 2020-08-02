<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use DB;
use DataTables;
use PublicFunction;

class ReadController extends Controller
{
    //
    private $permission;
    private $url;
    public function __construct(){
        $this->permission = "Users.admin,Core.admin";
        $this->url  = url('/users');
        $this->middleware("permission:$this->permission");
        $this->middleware('ajax');

    }

    public function index(Request $request){
        $array = [
            'title'  => 'Daftar User ',
            'url'    => $this->url.'/data',
            'file_name' => 'daftar user',
            'search'    => [
                ['type' => 'text', 'name' =>'username'],
                ['type' => 'text', 'name' =>'email'],
                ['type' => 'template', 'name' => 'role', 'param' => 'select', 'option' => [0, 'x']],
                ['type' => 'select', 'name' =>'status', 'option' =>
                    [
                        ['val' => '1', 'text' => 'Aktif'],
                        ['val' => '0', 'text' => 'Tidak Aktif']
                    ]
                ],
            ],
            'data'   => [
                ['title' => 'username', 'name' => 'username' ],
                ['title' => 'email', 'name' => 'email' ],
                ['title' => 'nama', 'name' => 'nama' ],
                ['title' => 'Action', 'name' => 'action', 'align' => 'center' ]
            ],
            'javascript' => 'function data_delete(id){
                    alertify.confirm("Anda yakin akan menonaktifkan user ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "POST",
                                url: "'.url('/users/delete').'/"+id+"?_token='.csrf_token().'",
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("user berhasil dinonaktifkan");
                                        ajaxLoad("'.url('/users').'");
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
                function data_enable(id){
                    alertify.confirm("Anda yakin akan mengaktifkan user ini?", function (e) {
                        if (e) {
                            $(".loading").show();
                            $.ajax({
                                type: "POST",
                                url: "'.url("/users/enable").'/"+id+"?_token='.csrf_token().'",
                                contentType: false,
                                success: function (data) {
                                    if(data == "success"){
                                        alertify.log("user berhasil diaktifkan");
                                        ajaxLoad("'.url("/users").'");
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

        $permission = PublicFunction::permission($request, array('Core.admin'));
        if ($permission['response'] == true)
        {
        $addarray  = ['create'    => $this->url.'/create'];
        $array = (object) array_merge((array) $array, (array) $addarray);
        }

        return PublicFunction::datatables_template($array, $request->group);
    	//return view('users::read');
    }

    public function data(Request $request)
    {

        $user   = $request->user;
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

        
        $GLOBALS['nomor'] = 1 + $request->get('start');

        // $val = 'all';
        // $pusat = "WHEN '0' THEN '0'";
        // if ($request->has('status')) {
        //     if($request->get('status') == '0'){
        //         $val = 'off';
        //         $pusat = "";
        //     }
        // }

        // $filter = "
        //     SELECT a.user_id, a.role_ID, a.id, a.val, a.instansi, a.kdsatker, a.kdkantor, a.kdbaes1, a.kddept,
        //         CASE a.id 
        //             WHEN '1' THEN a.KDKPPN 
        //             WHEN '4' THEN e.KDKPPN 
        //             WHEN '6' THEN g.KDKPPN 
        //             END AS kdkppn,
        //         CASE a.id 
        //             WHEN '5' THEN f.KDKANWIL 
        //             END AS kdkanwil
        //     FROM (
        //         SELECT a.*,
        //         CASE a.id WHEN '1' THEN b.kdsatker END AS kdsatker,
        //         CASE a.id WHEN '2' THEN c.kdbaes1 END AS kdbaes1,
        //         CASE a.id WHEN '3' THEN d.KDDEPT END AS kddept,
        //         CASE a.id WHEN '4' THEN e.KDKPPN END AS kdkppn,
        //         CASE a.id WHEN '5' THEN f.KDKANWIL END AS kdkanwil,
        //         CASE a.id WHEN '6' THEN g.kdkantor END AS kdkantor,
        //         CASE a.id
        //             $pusat
        //             WHEN '1' THEN b.kdsatker
        //             WHEN '2' THEN c.kdbaes1
        //             WHEN '3' THEN d.KDDEPT
        //             WHEN '4' THEN e.KDKPPN
        //             WHEN '5' THEN f.KDKANWIL
        //             WHEN '6' THEN g.kdkantor
        //         END AS instansi
        //         FROM (
        //             SELECT a.user_id, a.role_ID, 
        //                 CASE WHEN b.id IS NULL THEN '0' ELSE b.id END AS id,
        //                 CASE WHEN b.val IS NULL THEN '0' ELSE b.val END AS val
        //             from role_users a 
        //             left join (
        //                 SELECT USER_ID, ROLE_ID, id, val
        //                 FROM ROLE_USERS, 
        //                     json_table(USER_GROUPS, '$' 
        //                     columns(
        //                         id VARCHAR2(20) PATH '$.$val.id',
        //                         NESTED PATH '$.$val.val[*]' columns (
        //                             val PATH '$'
        //                         )
        //                     )
        //                 ) WHERE val is not null
        //             ) b on a.user_id = b.user_id and a.role_id = b.role_id
        //         ) a 
        //         LEFT JOIN t_satker b ON a.val = b.KDSATKER
        //         LEFT JOIN t_unit c ON a.val = c.KDBAES1
        //         LEFT JOIN T_DEPT d ON a.val = d.KDDEPT
        //         LEFT JOIN T_KPPN e ON a.val = e.KDKPPN
        //         LEFT JOIN T_KANWIL f ON a.val = f.KDKANWIL
        //         LEFT JOIN t_kantor g ON a.val = g.kdkantor
        //     ) a
        //     LEFT JOIN t_satker b ON a.KDSATKER = b.KDSATKER
        //     LEFT JOIN t_unit c ON a.KDBAES1 = c.KDBAES1
        //     LEFT JOIN T_DEPT d ON a.KDDEPT = d.KDDEPT
        //     LEFT JOIN T_KPPN e ON a.KDKPPN = e.KDKPPN
        //     LEFT JOIN T_KANWIL f ON a.KDKANWIL = f.KDKANWIL
        //     LEFT JOIN t_kantor g ON a.kdkantor = g.kdkantor
        // ";

        $filter  = "SELECT a.*, c.name as role from users a 
                    LEFT JOIN role_users b on a.id = b.user_id
                    LEFT JOIN roles c on b.role_id = c.id
                    where role_id <> '1'
                    order by username asc";

        $query = DB::table(DB::raw("($filter) a"));

        

        if($request->group != 'all'){
            $query = $query->wherein('role_id', $admin_of);
            // $query = PublicFunction::WhereGroup($request->group, $query);
        }


        // $filter_query   = $query->toSql();
        // $filter_binding = $query->getBindings();

        // //dd($filter_query);

        // $sql = "
        //     SELECT a.*, b.kode FROM users a right JOIN (
        //         SELECT a.user_id, LISTAGG(b.name || ': ' || a.kode, '; <br>') WITHIN GROUP (ORDER BY user_id) AS kode from (
        //             SELECT user_id, role_id, LISTAGG(instansi, ' - ') WITHIN GROUP (ORDER BY user_id, role_id) AS kode
        //             FROM(
        //                 $filter_query
        //             )
        //             GROUP BY USER_ID, role_id
        //         ) a 
        //         LEFT JOIN roles b on a.role_id = b.id
        //         GROUP BY user_id
        //     ) b ON a.id = b.user_id
        //     where b.user_id is not null";

        // //$sql = 'users';
        // $query = DB::table(DB::raw("($sql) a"))
        //         ->setBindings($filter_binding);

        // if ($request->has('role')) {
        //     if($request->get('role') != 'x'){
        //         $sql = 'select a.*, b.role_id as role_filter from ('.$query->toSql().') a left join role_users b on a.id = b.user_id';
        //         //where b.role_id = ?
        //         $query = DB::table(DB::raw("($sql) a"))
        //                 ->setBindings($filter_binding)
        //                 ->where('role_filter', $request->get('role'));
        //     }
        // }
        


        

        //$query = $query->whereIn('a.role_id', $admin_of);
        //if (app(PublicF::class)->permissionOnly(array('Users.admin_multi')) == false){
            //$query = app(PublicF::class)->WhereGroup($query);
        //}

        return DataTables::of($query)
            ->filter(function ($query) use ($request) {
                if ($request->has('username')) {
                    $query->where('username', 'like', "%{$request->get('username')}%");
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }

                
                if ($request->has('status')) {
                    if($request->get('status') == 1){
                        $query->where('aktif', 1)->whereNotNull('aktif');
                    }else if($request->get('status') == 2){
                        $query->where(function($query){
                            $query->whereNull('aktif')->orWhere('aktif', '<>', '1');
                        });
                        
                    }
                }
            })
        	->addColumn('nomor',function($query){
                return $GLOBALS['nomor']++;
            })
            ->addColumn('action', function ($query){
                /*if (app(PublicF::class)->permissionOnly(array('Users.admin_multi'))){
                    $action = '<a href="javascript:ajaxLoad(\''.url('/users/update_multi/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
                }else{*/
            	    $action = '<a href="javascript:ajaxLoad(\''.url('/users/update/'.$query->id).'\')" class="btn btn-xs btn-success" id="'.$query->id.'"><i class="fa fa-pencil"></i> update</a>';
                //}
                    // $action .= '<a href="javascript:ajaxLoad(\''.url('/users/role/'.$query->id).'\')" class="btn btn-xs btn-warning" id="'.$query->id.'"><i class="fa fa-key"></i> role</a>';
                    
                /*if($query->aktif == 1){
                    $action .= '<a class="btn btn-xs btn-danger" id="'.$query->id.'" onclick="data_delete('.$query->id.')"><i class="fa fa-trash"></i> Disable</a>';    
                }else{
                    $action .= '<a class="btn btn-xs btn-primary" id="'.$query->id.'" onclick="data_enable('.$query->id.')"><i class="fa fa-trash"></i> Enable </a>';
                }*/
            	
            	return $action;
            })
            ->rawColumns(['kode', 'action'])
            ->make(true);


    }
}

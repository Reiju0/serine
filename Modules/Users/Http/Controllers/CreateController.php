<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Validator;
use DB;
use PublicF;
use PublicFunction;
use PublicRef;

class CreateController extends Controller
{
    //
    private $roleJafung;
    public function __construct(){
        $this->middleware('permission:Core.admin');
        $this->middleware('ajax');
    }

    public function index(Request $request)
    {
    	# code...
    	//return view('users::create');
        $roles = DB::table('roles')->where('group_id', '1')->get();
        $role_array = "";
        
        $satker = DB::table('ref_satker')->where('status', '1')->orderBy('kdsatker', 'asc')->get();

        $option = [];
        foreach ($satker as $key) {
            # code...
            $option[] = ['val'   => $key->kdsatker, 'text' => $key->kdsatker.'. '.$key->nmsatker];
        }

        $array = [
            'title'     => 'Buat User',
            'url'       => url('/users/create'),
            'back'      => url('/users'),
            'input'     => [
                ['type' => 'html', 'title' => 'USERNAME', 'html' => '<input type="text" name="username" id="username" required class="form-control col-md-11" style="display:inline" placeholder="NIP/NIK/NRP"> <a href="javascript:cekUser()"><i class="fa fa-refresh"></a></i>'],
                ['type' => 'email', 'name' => 'email', 'required' => true],
                ['type' => 'text', 'name' => 'nama', 'required' => true],
                //['type' => 'text', 'name' => 'nip', 'text' => 'NIP', 'required' => true],
                ['type' => 'textarea', 'name' => 'alamat'],
                ['type' => 'text', 'name' => 'telepon', 'title' => 'Telp / Fax'],
                ['type' => 'template', 'name' => 'role', 'param' => 'select'],
                // ['title'=> 'KL', 'type' => 'template', 'name' => 'getRef', 'param' => 'select', 'option' => ['kddept'], 'div_class' => 'div_kl div_kode'],
                // ['type' => 'template', 'name' => 'kode', 'param' => 'select', 'option' => [$request->group, 0, 0], 'div_class' => 'div_kode'],
                ['type' => 'select', 'title' => 'kode satker', 'name' => 'kdsatker', 'option' => $option],
                //['type' => 'password', 'name' => 'password', 'div_class' => 'div_pass'],
                //['type' => 'password', 'name' => 'repassword', 'text' => 'Re Password', 'div_class' => 'div_pass'],
                //['type' => 'text', 'name' => 'kdsatker', 'text' => 'Kode Satker'],
            ],
            'form'      => '<style type="text/css"> .white{color:black; } .green{color:green; } .red{color:red; } </style> 
                            <div class="form-group" align="center">
                                <label id="divCheckPasswordMatch"  style="padding: 0 0 15px;" class="white">password matching ...</label>
                            </div>',
            'javascript'=> '
                            function checkPasswordMatch() {
                                var password = $("#password").val();
                                var confirmPassword = $("#repassword").val();

                                if (password != confirmPassword){
                                    $("#divCheckPasswordMatch").html("New Passwords do not match!").removeClass("white").removeClass("green").addClass("red");
                                    $("#submit").attr("disabled", "disabled").addClass("btn-default").removeClass("btn-primary");
                                }else{
                                    $("#divCheckPasswordMatch").html("New Passwords match.").removeClass("white").removeClass("red").addClass("green");
                                    $("#submit").removeAttr("disabled").addClass("btn-primary").removeClass("btn-default");
                                }

                            }

                            function cekUser(){
                                var username = $("#username").val();
                                $(".loading").show();
                                if(username == ""){
                                    alertify.log("Input Username (NIP/NIK/NRP) terlebih dahulu.");
                                    $(".loading").hide();
                                }else{
                                    $.ajax({
                                        url:"'.url('/users/cekUser').'/"+username,
                                        method: "GET",
                                        success:function(result){
                                            var obj = JSON.parse(result);
                                            obj.forEach(updateForEach);
                                            $(".loading").hide();

                                            if(obj == ""){
                                                alertify.log("Data tidak ditemukan.");
                                            }else{
                                                alertify.log("Data ditemukan. Data yang akan disimpan hanya perubahan role.");
                                            }
                                        },
                                        error:function (e, messages, detail){
                                            alertify.log(detail);
                                            $(".loading").hide();
                                        }
                                    }); 
                                }
                                
                            }

                            function updateForEach(item, index){
                                $("#"+item.div).val(item.value);
                            }
                            ',
            'jquery'    => 'jQuery("#repassword").keyup(checkPasswordMatch);
                            
                            
                            '
        ];

        return PublicFunction::create_template($array);
        
    }

    public function create(Request $request)
    {
    	# code...
    	
        $validator = Validator::make($request->all(), [
            'username'   => 'numeric',
            'email'      => 'email',
            'telepon'    => 'nullable|numeric',
            'kdnip'     =>'nullable|numeric'
        ], PublicRef::Validation_Message());

        if ($validator->fails()) {
            $return = "";
            $error = json_decode($validator->errors());
            foreach ($error as $key) {
                $return .= '<li>'.$key[0].'</li>';
            }
            return $return;
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

        $username   = PublicFunction::htmlTag($request->input('username'));
        $email   	= PublicFunction::htmlTag($request->input('email'));
        $nama            = PublicFunction::htmlTag($request->input('nama'));
        $jabatan        = PublicFunction::htmlTag($request->input('jabatan'));
        //$nip            = PublicFunction::htmlTag($request->input('nip'));
        $alamat         = PublicFunction::htmlTag($request->input('alamat'));
        $telepon        = PublicFunction::htmlTag($request->input('telepon'));
        //$password	= $request->input('password');
        
        $role_id	= $request->input('role');
        $role 		= DB::table('roles')->where('id', $role_id)->first();
        $group_id	= $role->group_id;
        // $group_value= $request->input('kode');
        $role       = Sentinel::findRoleById($role_id);
        $group = '0';
        

        if($group_id > 0){
            $group= $request->input('kdsatker');
        }
        // dd($group);


        $permission = PublicFunction::permission($request, ['superadmin']);
        if(!in_array($role_id, $admin_of) and $permission['response'] == false){
            return "you don't have permission for this role";
        }   
		

		$check_username = [
			'username' => PublicFunction::htmlTag($username),
		];
		$check_username = Sentinel::findByCredentials($check_username);

        $credentials = [
            'username'      => $username,
            'email'         => $email,
            'password'      => $username
        ];

        $data = [
            'nama'          => $nama,
            'nip'           => $username,
            'alamat'        => '',
            'telp'          => '',
            'aktif'         => 1,
        ];



		if(!($check_username)){
			try{

                DB::beginTransaction();

                $user = Sentinel::registerAndActivate($credentials);
                DB::table('users')->where('id', $user->id)->update($data);

                $role->users()->attach($user);
                DB::table('role_users')
                    ->where('user_id', $user->id)
                    ->where('role_id', $role_id)
                    ->update([
                        'user_groups' => $group
                    ]);
                DB::commit();

			}catch(Exception $e){
				return 'error occurred';
			}
            
		}else{
            $query = DB::table('role_users')->where('user_id', $check_username->id)->delete();

            $user   = $check_username;
            // $query->delete();
            $role->users()->attach($user);
            DB::table('role_users')
                ->where('user_id', $check_username->id)
                ->where('role_id', $role_id)
                ->update([
                    'user_groups' => $group
                ]);
            
			

		}

        return "success";


    }	

    public function cekUser(Request $request, $username){
        $user   = DB::table('users')->select('email', 'nama', 'nip', 'alamat', 'telp')->where('username', $username)->first();
        $return = array();

        if(!is_null($user)){
            $return[] = ['div' => 'email', 'value' => $user->email];
            $return[] = ['div' => 'nama', 'value' => $user->nama];
            $return[] = ['div' => 'nip', 'value' => $user->nip];
            $return[] = ['div' => 'alamat', 'value' => $user->alamat];
            $return[] = ['div' => 'telepon', 'value' => $user->telp];
        }

        return json_encode($return);
        
    }
}

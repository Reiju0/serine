<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PublicRef;
use DB;
use App\Jobs\SendGeneralEmail;

class PublicFunction extends Controller
{
    public function WhereGroupAll($query){
    	//$var = array(1,2,3,4,5);
        $user = Sentinel::check();
        $group = json_decode($user->user_group);
        //dd($group);
        if(isset($group->all)){
            foreach ($group->all as $key) {
                if(count($key->val) < 1){
                    $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                    $query = $query->orWhereIn('a.'.$user_group->kolom, ['0']);
                }
                else if(in_array(0, $key->val)){
                }else{
                    $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                    $query = $query->whereIn('a.'.$user_group->kolom, $key->val);
                }
            }
        }
        //dd($query);

    	return $query;
    }
	
	//
    public static function WhereGroup($groups, $query){

        if($groups != 'all'){
            $query = $query->where(function ($query) use ($groups){
                foreach ($groups as $group) {
                    $group = json_decode($group);
                    foreach ($group->all as $key) {
                        if(count($key->val) < 1){
                            $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                            $query = $query->orWhereIn('a.'.$user_group->kolom, ['0']);
                        }
                        else if(in_array(0, $key->val)){
                        }else{
                            $user_group = DB::table('user_group')->where('id', '=', $key->id)->first();
                            $query = $query->orWhereIn('a.'.$user_group->kolom, $key->val);
                        }
                    }
                }
            });
        }

        return $query;
    }

    public static function WhereGroupFilter($groups, $query){
        if($groups != 'all'){
            $query = $query->where(function ($query) use ($groups){
                $query = $query->orWhereIn('kdsatker', $groups);
            });
        }

        return $query;
    }

    public static function UserValidation(Request $request, $id, $permission){

        return true;
        

        //$role   = Sentinel::findById($id)->roles;

        $admins  = PublicFunction::adminof($request, explode(',',$permission));

        $value  = true;
        $array  = false;
        
            foreach ($admins as $admin) {
                # code...
                if(in_array($key->role_id, json_decode($admin['roleof'], true))){
                    $group = json_decode($key->user_groups);
                    foreach ($group->all as $keys) {
                        if(in_array(0, $keys->val)){
                            $value = false;
                        }else{
                            $user_group = DB::table('user_group')->where('id', '=', $keys->id)->first();
                            $query = DB::table($user_group->ref_table)->whereIn($user_group->kolom, $keys->val);

                            $count = $query->count();

                            $adming = json_decode($admin['group']);
                            foreach ($adming->all as $keyg) {
                                if(in_array(0, $keyg->val)){
                                }else{
                                    $ug = DB::table('user_group')->where('id', '=', $keyg->id)->first();
                                    $query = $query->WhereIn($ug->kolom, $keyg->val);
                                }
                            }
                            $count2 = $query->count();
                            if($count == $count2){
                                $array = true;
                            }else{
                                $value  = false;
                            }
                        }
                    }
                }else{
                    $value = false;
                }   
            }
        

        $return = true;
        if(($value == false) or ($array == false)){
            $return = false;
        }

        $roles = Sentinel::findById($request->user->id)->getRoles();
        foreach ($roles as $role) {
            if($role->slug == 'SuperAdministrator'){
                $return = true;
            }
        }

        //$return = (object) array('value' => $value, 'array' => $array);
        return $return;
    }

    public static function SatkerValidation($request, $kdsatker, $permission){
        //$role   = Sentinel::findById($id)->roles;

        $satker     = "SELECT a.*, b.kdkanwil from t_satker_blu a 
                        LEFT JOIN t_kppn b on a.kdkppn = b.kdkppn
                        WHERE a.kdsatker = ?";

        $query = DB::table(DB::raw("($satker) a"))
                ->setBindings([$kdsatker]);

        $akses      = PublicFunction::permission($request, $permission);

        $return     = false;
			
        if($akses['response'] == true){
            if($akses['group'] == 0){
                $return = true;
            }else{
                $query = PublicFunction::WhereGroup($akses['group'], $query);
                if($query->count() > 0){
                    $return = true;
                }
            }
        }

        return $return;

    }

    public static function adminOf($request, $permission){
        //$ = Sentinel::getUser()->getRoles();
        /*$roles = DB::table('role_users')
                        ->where('user_id', $id)
                        ->get();*/
        $roles = Sentinel::findById($request->user->id)->getRoles();
        $admin_of    = array();
        foreach ($roles as $role) {
            foreach ($permission as $perm) {
                # code...
                $array = array();
                if(array_key_exists($perm, $role->permissions)){
                    //array_push($admin_of, $role->admin_of);
                    $array['roleof'] = $role->admin_of;
                    $array['group'] = $role->getOriginal('pivot_user_groups');
                    array_push($admin_of, $array);
                }
            }
        }

        return $admin_of;
    }

    public static function permission($request, $permission){
        array_push($permission, 'admin', 'superadmin');
        $roles = Sentinel::getUser()->getRoles();
        $user_groups = false;
        foreach ($roles as $role) {
            foreach ($permission as $perm) {
                # code...
                if(array_key_exists($perm, $role->permissions)){
                    if($role->getOriginal('pivot_user_groups') == '0' or empty($role->getOriginal('pivot_user_groups'))){
                        $user_groups = 'all';
                        break; 
                    }else{
                        if(is_array($user_groups) == false){
                            $user_groups = array();
                        }
                        
                        array_push($user_groups, $role->getOriginal('pivot_user_groups'));                            
                    }
                    break;
                }
            }
        }

        $return = array();
        if($user_groups == 'all'){
            $return['response'] = true;
            $return['group']    = 0;
        }elseif($user_groups){
            $return['response'] = true;
            $return['group']    = $user_groups;
        }
        else{
           $return['response'] = false;
        }

        return $return;
    }

    public static function permissionOnly($request, $permission){
    	$roles = Sentinel::getUser()->getRoles();
        $user_groups = false;
        foreach ($roles as $role) {
            foreach ($permission as $perm) {
                # code...
                if(array_key_exists($perm, $role->permissions)){
                    if($role->getOriginal('pivot_user_groups') == '0' or empty($role->getOriginal('pivot_user_groups'))){
                        $user_groups = 'all';
                        break; 
                    }else{
                        $user_groups = true;
                        array_push($user_groups, $role->getOriginal('pivot_user_groups'));                            
                    }

                    break;
                }
            }
        }

        $return = array();
        if($user_groups == 'all'){
            $return['response'] = true;
            $return['group']    = 0;
        }elseif($user_groups){
            $return['response'] = true;
            $return['group']    = $user_groups;
        }
        else{
           $return['response'] = false;
        }

        return $return;
           
    }
	
	public function has_permission($role){
        array_push($role, 'admin', 'superadmin');
        if (Sentinel::hasAnyAccess($role)){
            return true;
        }else{
            return false;
        }    
    }

    public function has_permission_only($role){
        if (Sentinel::hasAnyAccess($role)){
            return true;
        }else{
            return false;
        }    
    }

    public static function get_parent()
    {
        $sql = "select * from menu where is_parent='1'";
         $query = DB::table(DB::raw("($sql) a"))
                ->selectRaw('a.*')
                ->orderBy('menu', 'asc');
        
        $query = $query->get();
        $count = $query->count();

        $option = "<option value=''>all</option>";
        if($count == 1){
            $option = "";
        }

        $sebelumnya = '';
        foreach ($query as $key) {
           $option .= "<option value='".$key->id."'>". $key->menu ."</option>";
        }
        return $option;

    }

    public static function getRole($role_id, $permission){
        array_push($permission, 'admin', 'superadmin');
        $user   = Sentinel::check();
        $roles   = Sentinel::findById($user->id)->roles;
        //dd($roles);[0]->permissions
        $admin_of = array();
        $superadmin = false;
        

        $query = DB::table('roles')
            ->where('slug', '<>', 'SuperAdministrator')
            ->orderBy('name', 'asc');

        // if($superadmin == false){
        //     $query = $query->whereIn('id', $admin_of);
        // }
        //$query = app(PublicF::class)->WhereRdid($query, 'ref_daerah');
        $query = $query->get();
        
        $role = "";
        //$role = "";
        foreach ($query as $key) {
            $role .= "<option value='".$key->id."' ";
            if($key->id == $role_id ){
                $role .= "selected";
            }
            $role .= ">".$key->name."</option>";
        }
        return $role;
    }


    public static function datatables_template($array, $groups, $advance = NULL){
        //dd($array);
        $array = json_decode(json_encode($array));
        $advance_filter = '';
        if(isset($array->advance_filter)){
            $advance_filter = PublicFunction::data_filter($groups); 
        }

        $advance_filter .= $advance;
        return view('master/datatables',[   'array' => $array, 'advance_filter' => $advance_filter]);
    }

    public static function tables_template($array, $groups, $advance = NULL){
        //dd($array);
        $array = json_decode(json_encode($array));
        $advance_filter = '';
        if(isset($array->advance_filter)){
            $advance_filter = PublicFunction::data_filter($groups); 
        }

        $advance_filter .= $advance;
        return view('master/tables',[   'array' => $array, 'advance_filter' => $advance_filter
                ]);
    }

    public static function datatables_detail_template($array, $groups, $advance = NULL){
        //dd($array);
        $array = json_decode(json_encode($array));
        $advance_filter = '';
        if(isset($array->advance_filter)){
            $advance_filter = PublicFunction::data_filter($groups); 
        }

        $advance_filter .= $advance;
        return view('master/datatables_detail',[   'array' => $array, 'advance_filter' => $advance_filter
                ]);
    }

    public static function create_template($array){
        //dd($array);
        $array = json_decode(json_encode($array));
        return view('master/create',['array' => $array]);
    }

    public static function update_template($array){
        //dd($array);
        $array = json_decode(json_encode($array));
        return view('master/update',['array' => $array]);
    }
	
    public static function view_template($array){
        //dd($array);
        $array = json_decode(json_encode($array));
        return view('master/view',['array' => $array]);
    }
	
	public static function proses_template($array){
        //dd($array);
        $array = json_decode(json_encode($array));
        return view('master/proses',['array' => $array]);
    }
	
	public static function verifikasi_template($array){
        //dd($array);
        $array = json_decode(json_encode($array));
        return view('master/verifikasi',['array' => $array]);
    }

    public static function SendGeneralEmail($slug, $kode, $subject, $isi){
        $role = DB::table('roles')->where('slug', $slug)->orWhere('id', $slug)->first();

        $sql = "SELECT * FROM users a LEFT JOIN (
                    SELECT USER_ID, ROLE_ID, GID, kode
                    FROM ROLE_USERS, json_table(USER_GROUPS, '$' columns(
                        GID VARCHAR2(20) PATH '$.all.id',
                        NESTED PATH '$.all.val[*]' columns (
                            kode PATH '$'
                        )
                    ))
                    WHERE ROLE_ID = ?
                    ) b ON a.id = b.user_id
                WHERE kode = ?
                    AND aktif = '1'";

        $query = DB::table(DB::raw("($sql) a"))
                ->setBindings([$role->id, $kode])
                ->get();

        foreach ($query as $user) {
            # code...

            if(empty($user->nama)){
                $nama = $user->username;
            }else{
                $nama = $user->nama;
            }
            $data   = [
                'subject'   => $subject,
                'nama'      => $nama,
                'email'     => $user->email,
                'isi'       => $isi
            ];

            dispatch(new SendGeneralEmail($data));

        }

        
    }

    public static function htmlTag($html){
        $html = strip_tags($html, 
            "
                <a>
                <b>
                <blockquote>
                <code>
                <del>
                <dd>
                <div>
                <dl>
                <em>
                <font>
                <h1><h2><h3><h4><h5>
                <i>
                <img>
                <kbd>
                <label>
                <li>
                <ol>
                <p>
                <pre>
                <u>
                <span>
                <sup>
                <sub>
                <strong>
                <strike>
                <table>
                <td>
                <th>
                <tr>
                <ul>
                <br>
                <hr>
            ");
        return $html;
    }
	
	public static function WhereById($user_id,$role_id,$query){
				
		$data=DB::table('role_users')
				  ->where('user_id',$user_id)
				  ->where('role_id',$role_id);
		
		$cek=$data->count();
		
		if($cek!==0){
			$get_user=$data->first();
			$group = json_decode($get_user->user_groups);
			
			if(isset($group->all)){
				foreach ($group->all as $key) {
					$kode=$key->val;
					$id=$key->id;
				}
			}
			
			$kode=implode(",",$kode);
						
			$user_group = DB::table('user_group')->where('id', '=', $id)->first();
            $query = $query->Where('a.'.$user_group->kolom,$kode);
		}
        
		return $query;
	}
	
	public static function get_kode($user_id,$role_id){
				
		$data=DB::table('role_users')
				  ->where('user_id',$user_id)
				  ->where('role_id',$role_id);
		
		$cek=$data->count();
		
		$kode="";
		
		if($cek!==0){
			$get_user=$data->first();
			$group = json_decode($get_user->user_groups);
			
			if(isset($group->all)){
				foreach ($group->all as $key) {
					$kode=$key->val;
					$id=$key->id;
				}
			}
			
			$kode=implode(",",$kode);
		}
        
		return $kode;
	}

    public static function digitConnect($url,  $json = null, $method = 'get'){
        $token = PublicFunction::digitToken();
        if($token == false){
            return $token;
        }

        $url_portal = \config('oauth2.server');

        $handle = curl_init($url_portal.'/'.$url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        if($method != 'post'){
            curl_setopt($handle, CURLOPT_POST, false);            
        }

        if($method == 'put'){
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        if($json != null){
            curl_setopt($handle, CURLOPT_POSTFIELDS, $json);
        }
        curl_setopt($handle, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token, 'Content-Type: application/json'));
        $resp = curl_exec($handle);
        
        curl_close($handle);

        if(json_decode($resp)){
            return json_decode($resp);
        }else{
            return false;
        }

        
    }


    public static function errorMessage($messageCode = 'MSG00000'){
        $array = [
            'MSG10201'  => 'Data terkirim terlalu besar',

            'MSG20001'  => 'Data ditemukan',
            'MSG20002'  => 'Data tidak ditemukan',
            'MSG20003'  => 'Data berhasil disimpan',
            'MSG20004'  => 'Request token berhasil',
            'MSG20005'  => 'Parameter tidak valid',

            'MSG40001'  => 'Parameter kosong',
            'MSG40101'  => 'Otentikasi gagal, kode satker salah',
            'MSG40102'  => 'Otentikasi gagal, key salah',
            'MSG40103'  => 'Token invalid atau sudah tidak berlaku',
            'MSG40301'  => 'Username / Password salah',
            'MSG40401'  => 'Resource tidak ditemukan',
            'MSG40501'  => 'Method Not Allowed',

            'MSG50001'  => 'Controller error, silahkan hubungi administrator',
            'MSG50002'  => 'Digit connection error',
            'MSG50301'  => 'Server Maintenance'
        ];

        $return = [
            'status'    => $messageCode,
            'message'   => 'Error message not found'
        ];

        if ($array[$messageCode] != null) {
            $return['message']    = $array[$messageCode];
        }

        return $return;

        
    }
}

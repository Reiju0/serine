<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Http\Requests;
use DB;

class InstallController extends Controller
{
    //
    public function index(){
    	$roles = DB::table('roles')->count();
    	if($roles > 0){
    		return redirect()->guest('login');
    	}else{
    		return view('install');
    	}
    }

    public function addSuperAdmin(Request $request){
    	$roles = DB::table('roles')->count();
    	if($roles < 1){
    		/*DB::table('users')->insert([
    				'ID'		=> 'seq_user.nextval',
    				'username'	=> PublicFunction::htmlTag($request->input('username')),
				    'email'    => PublicFunction::htmlTag($request->input('email')),
				    'password' => $request->input('password')
    			]);*/
		    $credentials = [
				'username'	=> PublicFunction::htmlTag($request->input('username')),
			    'email'    => PublicFunction::htmlTag($request->input('email')),
			    'password' => $request->input('password'),
			];

			DB::beginTransaction();

			$user = Sentinel::registerAndActivate($credentials);
			// DB::table('users')->where('id', $user->id)->update([
			// 	'aktif' = 1;
			// ]);
			
			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'SuperAdministrator',
			    'slug' => 'SuperAdministrator',
			]);

			$role->users()->attach($user);

			$role->permissions = [
			    'admin'			=> true,
			    'superadmin'	=> true,
			];

			$role->save();

			DB::table('options')->insert([
				['keys'	=> 'maintenance', 'val'	=> '0'],
				['keys'	=> 'message', 'val'	=> 'tidak ada message'],
				['keys'	=> 'version', 'val'	=> '0'],
				['keys'	=> 'token', 'val'	=> '0'],

			]);

			DB::table('permission')->insert([
				'module'	=>'Core',
				'permission'	=>'Core.admin',
			]);

			DB::table('permission')->insert([
				'module'	=>'Ref',
				'permission'	=>'Ref.admin',
			]);
			DB::table('permission')->insert([
				'module'	=>'Ref',
				'permission'	=>'Ref.read',
			]);

			DB::table('menu')->insert([
				['menu'	=> 'Referensi', 'is_parent' => '1', 'parent_id' => '0', 'permission' => 'Core.admin,Ref.read,Ref.admin,admin', 'status' => '1', 'have_link' => '0', 'path' => '-', 'style' => 'fa fa-list', 'delta' => '99'],
				['menu'	=> 'Admin', 'is_parent' => '1', 'parent_id' => '0', 'permission' => 'Core.admin,admin', 'status' => '1', 'have_link' => '0', 'path' => '-', 'style' => 'fa fa-user', 'delta' => '999']
			]);

			$ref = DB::table('menu')->where('menu', 'Referensi')->first();
			DB::table('menu')->insert([
				['menu'	=> 'Ref Satker', 'is_parent' => '0', 'parent_id' => $ref->id, 'permission' => 'Core.admin,Ref.read,Ref.admin,admin', 'status' => '1', 'have_link' => '1', 'path' => 'ref/satker', 'style' => 'fa fa-list', 'delta' => '1'],
				['menu'	=> 'Ref To Do List', 'is_parent' => '0', 'parent_id' => $ref->id, 'permission' => 'Core.admin,Ref.read,Ref.admin,admin', 'status' => '1', 'have_link' => '1', 'path' => 'ref/todo', 'style' => 'fa fa-list', 'delta' => '2'],
			]);

			$admin = DB::table('menu')->where('menu', 'Admin')->first();
			DB::table('menu')->insert([
				['menu'	=> 'Users', 'is_parent' => '0', 'parent_id' => $admin->id, 'permission' => 'Core.admin,admin', 'status' => '1', 'have_link' => '1', 'path' => 'users', 'style' => 'fa fa-list', 'delta' => '1'],
			]);



			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'Admin',
			    'slug' => 'Admin',
			]);
			$role->permissions = [
			    'Core.admin' => true,
			    'Ref.admin' => true,
			    'Ref.read' => true,
			];

			$role->save();

			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'Satker',
			    'slug' => 'Satker',
			]);
			$role->permissions = [
			    'Ref.read' => true,
			];

			$role->save();

			DB::table('roles')->where('slug', 'Satker')->update(['group_id' => 1]);

			/*


			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'Peserta',
			    'slug' => 'Peserta',
			]);


			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'Penilai',
			    'slug' => 'Penilai',
			]);


			$role = Sentinel::getRoleRepository()->createModel()->create([
			    'name' => 'Pengawas',
			    'slug' => 'Pengawas',
			]);
			*/
			DB::commit();
		}
		return redirect()->guest('login');
    }
}

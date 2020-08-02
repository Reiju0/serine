<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
	//Route::get('/etc', 'EtcController@index');
	Route::get('/', 'InstallController@index');
	Route::post('/createuser', 'InstallController@addSuperAdmin');
	Route::post('/login', 'LoginController@LogIn');
	Route::get('/login', 'OAuth2Controller@index');
	Route::get('/oauth2callback', 'OAuth2Controller@callback');
	Route::get('/login/1', 'LoginController@LogInCheckOut');
	Route::get('/logout', 'LoginController@Logout');

Route::group(['middleware' => 'auth'], function(){
	Route::get('/home','HomeController@View');
	Route::get('/profile', 'ProfileController@index');
	Route::post('/profile', 'ProfileController@update');
	//Route::get('/password', 'ProfileController@password');
	//Route::post('/password', 'ProfileController@updatePassword');

	Route::group(['prefix' => 'user'],function(){
		/*Route::get('/role', 'User\RoleController@index');
		Route::get('/role/data', 'User\RoleController@data');
		Route::get('/role/add', 'User\RoleController@create');
		Route::get('/role/kode/{rid}/{kddept}/{selected}', 'User\RoleController@getKode');
		Route::get('/role/update/{rid}', 'User\RoleController@update');
		Route::get('/role/update/{rid}/data', 'User\RoleController@update_data');
		Route::post('/role/create', 'User\RoleController@do_create');
		Route::get('/role/delete/{role_id}', 'User\RoleController@delete_role');
		*/
		Route::get('/role/group/{role_id}', 'User\GroupController@index');
		Route::get('/role/group/data/{role_id}', 'User\GroupController@data');
		Route::get('/role/group/add/{role_id}', 'User\GroupController@create');
		Route::post('/role/group/create/{role_id}', 'User\GroupController@do_create');
		Route::get('/role/group/delete/{role_id}/{kode}', 'User\GroupController@delete');
	});


	Route::group(['middleware' => 'permission:admin, Core.permission_admin', 'prefix' => 'permission'],function(){
		Route::get('/', 'Permission\ReadController@index');
		Route::get('/data', 'Permission\ReadController@data');
		Route::get('/create', 'Permission\CreateController@index');
		Route::post('/create', 'Permission\CreateController@create');
		Route::get('/update/{id}', 'Permission\UpdateController@index');
		Route::post('/update', 'Permission\UpdateController@update');
		Route::get('/delete/{id}', 'Permission\PermissionController@DeleteData');
	});

	Route::group(['middleware' => 'permission:admin, Core.role_admin', 'prefix' => 'role'],function(){
		Route::get('/', 'Role\ReadController@index');
		Route::get('/data', 'Role\ReadController@data');
		Route::get('/create', 'Role\CreateController@index');
		Route::post('/create', 'Role\CreateController@create');
		Route::get('/update/{id}', 'Role\UpdateController@index');
		Route::post('/update', 'Role\UpdateController@update');
		Route::get('/delete/{id}', 'Role\RoleController@DeleteData');
		Route::get('/permission/{id}', 'Role\UpdateController@permission');
		Route::post('/permission', 'Role\UpdateController@permissionUpdate');
	});

	Route::group(['middleware' => 'permission:admin, Core.menu_admin', 'prefix' => 'menu'],function(){
		Route::get('/', 'Menu\ReadController@index');
		Route::get('/data', 'Menu\ReadController@data');
		Route::get('/create', 'Menu\CreateController@index');
		Route::post('/create', 'Menu\CreateController@create');
		Route::get('/update/{id}', 'Menu\UpdateController@index');
		Route::post('/update', 'Menu\UpdateController@update');
		Route::get('/delete/{id}', 'Menu\DeleteController@DeleteData');

	});

	Route::group(['prefix' => 'option'],function(){
		Route::get('/', 'Option\ReadController@index');
		Route::get('init', 'Option\ReadController@init');
		Route::post('/update/{key}', 'Option\ReadController@update');

	});
});

<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'users', 'namespace' => 'Modules\Users\Http\Controllers'], function()
{
    Route::get('/', 'ReadController@index');
	Route::get('/data', 'ReadController@data');
	Route::get('/create', 'CreateController@index');
	Route::post('/create', 'CreateController@create');
	Route::get('/update/{id}', 'UpdateController@index');
	Route::post('/update', 'UpdateController@update');
	Route::get('/password/{id}', 'PasswordController@password');
	Route::post('/password', 'PasswordController@updatePassword');
	Route::post('/delete/{userId}/{roleId}/{val}', 'UsersController@DeleteData');
	Route::post('/enable/{userId}/{roleId}/{val}', 'UsersController@EnableData');
	Route::get('/group/{id}/{selected}', 'UserController@getRoleGroup');

	// Route::get('/role/{id}', 'RoleController@index');
	// Route::get('/role/data/{id}', 'RoleController@data');
	// Route::get('/role/add/{id}', 'RoleController@create');
	Route::get('/role/kode/{id}/{kddept}/{selected}', 'RoleController@getKode');
	// Route::get('/role/update/{id}/{rid}', 'RoleController@update');
	// Route::get('/role/update/{id}/{rid}/data', 'RoleController@update_data');
	// Route::post('/role/create/{id}', 'RoleController@do_create');
	// Route::get('/role/delete/{id}/{role_id}', 'RoleController@delete_role');

	Route::get('/role/group/{id}/{role_id}', 'GroupController@index');
	Route::get('/role/group/data/{id}/{role_id}', 'GroupController@data');
	Route::get('/role/group/add/{id}/{role_id}', 'GroupController@create');
	Route::post('/role/group/create/{id}/{role_id}', 'GroupController@do_create');
	Route::get('/role/group/delete/{id}/{role_id}/{kode}', 'GroupController@delete');

	Route::get('/permission/{id}', 'PermissionController@permission');
	Route::post('/permission/{id}', 'PermissionController@permissionUpdate');

	Route::get('/cekUser/{username}', 'CreateController@cekUser');
});

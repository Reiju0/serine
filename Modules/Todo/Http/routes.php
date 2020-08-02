<?php

Route::group(['middleware' => 'web', 'prefix' => 'todo', 'namespace' => 'Modules\Todo\Http\Controllers'], function()
{
    //read
	Route::get('/', 'TodoController@index');
    Route::get('/data', 'TodoController@data');
	
	Route::get('/detil/{id_mass}', 'TodoController@index_detil');
    Route::get('/detil/data/{id_mass}', 'TodoController@data_detil');
	
	//create
	Route::get('/create', 'TodoAdminController@create');
	Route::post('/create', 'TodoAdminController@do_create');
	
	Route::get('/detil/create/{mass_id}', 'TodoAdminController@create_detil');
	Route::post('/detil/create', 'TodoAdminController@do_create_detil');
	
	//update
	Route::get('/update/{mass_id}', 'TodoAdminController@update');
	Route::post('/update', 'TodoAdminController@do_update');
	
	//delete
	Route::get('/hapus/{id}', 'TodoAdminController@do_delete');
	Route::get('/hapus_detil/{id}', 'TodoAdminController@do_delete_detil');
});

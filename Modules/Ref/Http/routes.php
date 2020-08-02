<?php


Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'ref', 'namespace' => 'Modules\Ref\Http\Controllers'], function()
{

	Route::group(['prefix' => 'satker'], function()
	{
	    Route::get('/', 'SatkerController@index');
	    Route::get('/data', 'SatkerController@data');
	    Route::get('/create', 'SatkerAdminController@create');
	    Route::post('/create', 'SatkerAdminController@do_create');
	    Route::get('/update/{id}', 'SatkerAdminController@update');
	    Route::post('/update/{id}', 'SatkerAdminController@do_update');
	    Route::get('/enable/{id}', 'SatkerAdminController@enable');
	    Route::get('/disable/{id}', 'SatkerAdminController@disable');
	});

	Route::group(['prefix' => 'todo'], function()
	{
	    Route::get('/', 'ToDoController@index');
	    Route::get('/data', 'ToDoController@data');
	    Route::get('/create', 'ToDoAdminController@create');
	    Route::post('/create', 'ToDoAdminController@do_create');
	    Route::get('/update/{id}', 'ToDoAdminController@update');
	    Route::post('/update/{id}', 'ToDoAdminController@do_update');
	    Route::get('/enable/{id}', 'ToDoAdminController@enable');
	    Route::get('/disable/{id}', 'ToDoAdminController@disable');
	});
	

});

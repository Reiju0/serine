<?php

Route::group(['middleware' => 'web', 'prefix' => 'dashboard', 'namespace' => 'Modules\Dashboard\Http\Controllers'], function()
{
    Route::get('/switcher', 'DashboardController@switcher');
	
	//dashboard satker
    Route::group(['prefix' => 'satker'], function()
    {
        Route::get('/', 'DashboardSatkerController@index');
        Route::get('/data', 'DashboardSatkerController@data');

        Route::get('/detail/{id}', 'DashboardSatkerController@detail');

        //action
        Route::get('/done/{id}', 'DashboardSatkerAdminController@done');
        Route::post('/done/{id}', 'DashboardSatkerAdminController@do_done');

    });
});

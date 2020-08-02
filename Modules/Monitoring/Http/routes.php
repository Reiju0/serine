<?php

Route::group(['middleware' => 'web', 'prefix' => 'monitoring', 'namespace' => 'Modules\Monitoring\Http\Controllers'], function()
{
    Route::get('/per_satker', 'MonitoringController@per_satker');
    Route::get('/per_satker/data', 'MonitoringController@data_per_satker');
});

<?php

Route::group(['middleware' => 'web', 'prefix' => 'goods', 'namespace' => 'Modules\Goods\Http\Controllers'], function()
{
    Route::get('/', 'GoodsController@index');
});

<?php

Route::group(['namespace' => 'Botble\Accountingsystem\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'accountingsystems', 'as' => 'accountingsystem.'], function () {
            Route::resource('', 'AccountingsystemController')->parameters(['' => 'accountingsystem']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'AccountingsystemController@deletes',
                'permission' => 'accountingsystem.destroy',
            ]);
        });
    });

});

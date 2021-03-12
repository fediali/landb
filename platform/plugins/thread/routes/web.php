<?php

Route::group(['namespace' => 'Botble\Thread\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threads', 'as' => 'thread.'], function () {
            Route::resource('', 'ThreadController')->parameters(['' => 'thread']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadController@deletes',
                'permission' => 'thread.destroy',
            ]);
            Route::get('clone-item/{id}', [
                'as'         => 'cloneItem',
                'uses'       => 'ThreadController@cloneItem',
                'permission' => 'thread.create',
            ]);
            Route::get('details/{id}', [
                'as'         => 'details',
                'uses'       => 'ThreadController@show',
                'permission' => 'thread.create',
            ]);
        });
    });

});

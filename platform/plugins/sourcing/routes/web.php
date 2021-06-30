<?php

Route::group(['namespace' => 'Botble\Sourcing\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'sourcings', 'as' => 'sourcing.'], function () {
            Route::resource('', 'SourcingController')->parameters(['' => 'sourcing']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'SourcingController@deletes',
                'permission' => 'sourcing.destroy',
            ]);
            Route::get('delete/{id}', [
                'as'         => 'delete',
                'uses'       => 'SourcingController@destroy',
                'permission' => 'sourcing.delete',
            ]);
            Route::get('detail/{id}', [
                'as'         => 'detail',
                'uses'       => 'SourcingController@show',
                'permission' => 'sourcing.detail',
            ]);
        });
    });

});

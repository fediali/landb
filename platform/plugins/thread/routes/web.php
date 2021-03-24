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
            Route::post('add-variations', [
                'as'         => 'addVariation',
                'uses'       => 'ThreadController@addVariation',
                'permission' => 'thread.create',
            ]);
            Route::post('postComment', [
                'as'         => 'postComment',
                'uses'       => 'ThreadController@postComment',
                'permission' => 'thread.create',
            ]);
            Route::post('addVariationPrints', [
                'as'         => 'addVariationPrints',
                'uses'       => 'ThreadController@addVariationPrints',
                'permission' => 'thread.create',
            ]);
            Route::get('updateVariationStatus/{id}/{status}', [
                'as'         => 'updateVariationStatus',
                'uses'       => 'ThreadController@updateVariationStatus',
                'permission' => 'thread.create',
            ]);
            Route::get('removeFabric/{id}', [
                'as'         => 'removeFabric',
                'uses'       => 'ThreadController@removeFabric',
                'permission' => 'thread.create',
            ]);
            Route::post('change-status', [
                'as'         => 'changeStatus',
                'uses'       => 'ThreadController@changeStatus',
                'permission' => 'thread.create',
            ]);
            Route::get('removeVariation/{id}', [
                'as'         => 'removeVariation',
                'uses'       => 'ThreadController@removeVariation',
                'permission' => 'thread.create',
            ]);
            Route::get('removeThreadSpecFile/{id}', [
                'as'         => 'removeThreadSpecFile',
                'uses'       => 'ThreadController@removeThreadSpecFile',
            ]);
            Route::get('orderItem/{id}', [
                'as'         => 'orderItem',
                'uses'       => 'ThreadController@pushToEcommerce',
                'permission' => 'thread.create',
            ]);
            Route::get('pushEvent', function (){
              broadcast(new \App\Events\NotifyManager(\App\Models\User::find(1) , \Botble\Thread\Models\Thread::find(3)));
              return 'ok';
            });
        });
    });

});

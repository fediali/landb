<?php

Route::group(['namespace' => 'Botble\Thread\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'threads', 'as' => 'thread.'], function () {
            Route::resource('', 'ThreadController')->parameters(['' => 'thread']);

            Route::post('save-advance-search/{type}', [
                'as'         => 'save.advance.search',
                'uses'       => 'ThreadController@saveAdvanceSearch',
                'permission' => 'thread.create',
            ]);
            Route::post('addPPsample', [
                'as'         => 'addPPsample',
                'uses'       => 'ThreadController@variationPPSample',
                'permission' => 'thread.create',
            ]);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ThreadController@deletes',
                'permission' => 'thread.destroy',
            ]);
            Route::get('clone-item/{id}', [
                'as'         => 'cloneItem',
                'uses'       => 'ThreadController@cloneItem',
                'permission' => 'thread.cloneItem',
            ]);
            Route::get('details/{id}', [
                'as'         => 'details',
                'uses'       => 'ThreadController@show',
                'permission' => 'thread.details',
            ]);

            Route::post('read-notification/', [
                'as'         => 'readNotification',
                'uses'       => 'ThreadController@readNotification',
                'permission' => 'thread.details',
            ]);
            Route::post('add-variations', [
                'as'         => 'addVariation',
                'uses'       => 'ThreadController@addVariation',
                'permission' => 'thread.create',
            ]);
            Route::post('edit-variations', [
                'as'         => 'editVariation',
                'uses'       => 'ThreadController@editVariation',
                'permission' => 'thread.edit',
            ]);
            Route::post('postComment', [
                'as'         => 'postComment',
                'uses'       => 'ThreadController@postComment',
                'permission' => 'thread.details',
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
            Route::post('addVariationTrim', [
                'as'         => 'addVariationTrim',
                'uses'       => 'ThreadController@addVariationTrim',
                'permission' => 'thread.create',
            ]);
            Route::get('removeVariationTrim/{id}', [
                'as'         => 'removeVariationTrim',
                'uses'       => 'ThreadController@removeVariationTrim',
                'permission' => 'thread.create',
            ]);
            Route::get('removeThreadSpecFile/{id}', [
                'as'         => 'removeThreadSpecFile',
                'uses'       => 'ThreadController@removeThreadSpecFile',
                'permission' => 'thread.edit',
            ]);
            Route::get('orderItem/{id}', [
                'as'         => 'orderItem',
                'uses'       => 'ThreadController@pushToEcommerce',
                'permission' => 'thread.create',
            ]);
            Route::get('pushEvent', function () {
                event(new \App\Events\ThreadApproval());
                return 'ok';
            });
            Route::post('add-pvt-cat-sizes-qty', [
                'as'         => 'addPvtCatSizesQty',
                'uses'       => 'ThreadController@addPvtCatSizesQty',
                'permission' => 'thread.create',
            ]);

            Route::get('download-tech-pack', [
                'as'         => 'download.tech.pack',
                'uses'       => 'ThreadController@downloadTechPack',
                'permission' => 'thread.details',
            ]);

        });
    });

});

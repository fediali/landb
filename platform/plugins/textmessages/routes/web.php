<?php

Route::group(['namespace' => 'Botble\Textmessages\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'textmessages', 'as' => 'textmessages.'], function () {
            Route::resource('', 'TextmessagesController')->parameters(['' => 'textmessages']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'TextmessagesController@deletes',
                'permission' => 'textmessages.destroy',
            ]);
        });
    });

});

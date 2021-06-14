<?php

Route::group(['namespace' => 'Botble\Chating\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'chatings', 'as' => 'chating.'], function () {
            Route::resource('', 'ChatingController')->parameters(['' => 'chating']);


            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'ChatingController@deletes',
                'permission' => 'chating.destroy',
            ]);

            Route::get('chat-room', [
                'as'         => 'chatRoom',
                'uses'       => 'ChatingController@chatRoom',
                'permission' => 'chating.create',
            ]);
            Route::get('sms-campaign', [
                'as'         => 'smsCampaign',
                'uses'       => 'ChatingController@smsCampaign',
                'permission' => 'chating.create',
            ]);
            Route::get('messages/{ids}', [
                'as'         => 'messages.chat',
                'uses'       => 'ChatingController@chatMessage',
                'permission' => 'chating.create',
            ]);
            Route::post('generate-token', [
                'as'         => 'generate.token',
                'uses'       => 'ChatingController@generateToken',
                'permission' => 'chating.create',
            ]);
            Route::post('send-sms', [
                'as'         => 'send.sms',
                'uses'       => 'ChatingController@sendSMS',
                'permission' => 'chating.create',
            ]);
            Route::post('get-sms', [
                'as'         => 'get.sms',
                'uses'       => 'ChatingController@getSMS',
                'permission' => 'chating.create',
            ]);

        });
    });

});

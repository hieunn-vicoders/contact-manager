<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => config('contact.namespace')], function ($api) {
        $api->resource('contacts', 'VCComponent\Laravel\Contact\Http\Controllers\Api\Frontend\ContactController');

        $api->group(['prefix' => 'admin'], function ($api) {
            $api->put('contacts/status/bulk', 'VCComponent\Laravel\Contact\Http\Controllers\Api\Admin\ContactController@bulkUpdateStatus');
            $api->put('contacts/status/{id}', 'VCComponent\Laravel\Contact\Http\Controllers\Api\Admin\ContactController@updateStatus');
            $api->resource('contacts', 'VCComponent\Laravel\Contact\Http\Controllers\Api\Admin\ContactController');
        });
    });
});

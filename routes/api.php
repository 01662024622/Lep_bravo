<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Route::group(['prefix' => 'v1'], function() {

//     Route::get('product/table', 'DataBaseApi\DataTableController@product')->name('product.api.data');

// });
Route::group(['prefix' => 'v1'], function() {

    Route::post('webhook/nhanh/erp', 'WebhookController@create')->name('webhook.api.nhanh');
    Route::get('webhook/nhanh/erp', 'WebhookController@get')->name('webhook.api.nhanh');
});

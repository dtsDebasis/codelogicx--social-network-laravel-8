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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1/', 'middleware' => ['api']], function () {
    //Auth APIs
    Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
    Route::post('login', 'App\Http\Controllers\Api\AuthController@login');

    Route::middleware('auth:api')->group(function () {
        Route::get('me', 'App\Http\Controllers\Api\ProfileController@me');

        Route::post('send_friend_request', 'App\Http\Controllers\Api\FriendshipController@send_friend_request');
        Route::get('get_friend_requests', 'App\Http\Controllers\Api\FriendshipController@get_friend_requests');
        Route::post('accept_reject_friend_request', 'App\Http\Controllers\Api\FriendshipController@accept_reject_friend_request');
    });
});


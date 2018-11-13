<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => ['cors']], function () {

    Route::apiResource('mallnavs', 'Api\Malls\MallNavController');
    Route::apiResource('mallgoods', 'Api\Malls\MallGoodController');
    Route::apiResource('activitys','Api\Activities\ActivityController');
    //会员卡
    Route::post('/members/changeIntegral', 'Api\Members\MemberController@changeIntegral');
    Route::post('/members/join', 'Api\Members\MemberController@join');
    Route::get('/members/{member_id}/selectTag', 'Api\Members\MemberController@selectTag');
    Route::post('/members/addTag', 'Api\Members\MemberController@addTag');
    Route::post('/members/deleteTag', 'Api\Members\MemberController@deleteTag');
    Route::apiResource('/members', 'Api\Members\MemberController');
    //会员卡设置
    Route::apiResource('member_settings', 'Api\Members\SettingController');
    //会员标签
    Route::apiResource('member_tags', 'Api\Members\TagController');


});
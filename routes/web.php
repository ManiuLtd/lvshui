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
    Route::group(['prefix' => 'member'], function() {
        //会员卡
        Route::post('members/change-integral', 'Api\Members\MemberController@changeIntegral');
        Route::post('members/join', 'Api\Members\MemberController@join');
        Route::get('members/{member}/select-tag', 'Api\Members\MemberController@selectTag');
        Route::post('members/add-tag', 'Api\Members\MemberController@addTag');
        Route::post('members/delete-tag', 'Api\Members\MemberController@deleteTag');
        Route::apiResource('members', 'Api\Members\MemberController');
         //会员卡设置
         Route::apiResource('settings', 'Api\Members\SettingController');
         //会员充值设置
         Route::apiResource('join/settings', 'Api\Members\JoinSettingController');
         //会员标签
         Route::apiResource('tags', 'Api\Members\TagController');
    });
    
    Route::group(['prefix' => 'coupon'], function() {
         //优惠券记录
        Route::get('records/get-user-coupons', 'Api\Coupons\RecordController@get_user_coupons');
        Route::apiResource('records', 'Api\Coupons\RecordController');
        //优惠券
        Route::apiResource('coupons', 'Api\Coupons\ConponController');
    });
    Route::group(['prefix' => 'sign'], function() {
        Route::get('get-sign','Api\Fans\SignInController@get_sign');
        Route::post('sign-in','Api\Fans\SignInController@signIn');
        Route::apiResource('tasks','Api\Fans\SignInController');
    });
    //个性定制
    Route::apiResource('activity/diys','Api\Activities\DiyAcitvityController');
    //活动
    Route::apiResource('activity/activitys','Api\Activities\ActivityController');
});

Route::get('authorize', 'Api\Fans\FanController@oauth');
Route::get('oauth-callback', 'Api\Fans\FanController@oauthCallback');

Route::get('wechat', function() {
    $user = session('target_url');
    return $user;
});


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

    //会员卡
    Route::post('/members/change-integral', 'Api\Members\MemberController@changeIntegral');
    Route::post('/members/join', 'Api\Members\MemberController@join');
    Route::get('/members/{member_id}/select-tag', 'Api\Members\MemberController@selectTag');
    Route::post('/members/add-tag', 'Api\Members\MemberController@addTag');
    Route::post('/members/delete-tag', 'Api\Members\MemberController@deleteTag');
    Route::apiResource('/members', 'Api\Members\MemberController');
    //会员卡设置
    Route::apiResource('member-settings', 'Api\Members\SettingController');
    //会员充值设置
    Route::apiResource('member-join-settings', 'Api\Members\JoinSettingController');
    //会员标签
    Route::apiResource('member-tags', 'Api\Members\TagController');
    
    //优惠券
    Route::apiResource('coupons', 'Api\Coupons\ConponController');
    //优惠券记录
    Route::get('coupon-records/get-user-coupons', 'Api\Coupons\RecordController@get_user_coupons');
    Route::apiResource('coupon-records', 'Api\Coupons\RecordController');
    

    //个性定制
    Route::apiResource('diy-activitys','Api\Activities\DiyAcitvityController');
    //活动
    Route::apiResource('activitys','Api\Activities\ActivityController');
});

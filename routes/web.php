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

    Route::group(['prefix' => 'member'], function () {
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

    Route::group(['prefix' => 'coupon'], function () {
        //优惠券记录
        Route::get('records/get-user-coupons', 'Api\Coupons\RecordController@get_user_coupons');
        Route::apiResource('records', 'Api\Coupons\RecordController');
        //优惠券
        Route::apiResource('coupons', 'Api\Coupons\ConponController');
    });
    Route::group(['prefix' => 'sign'], function () {
        Route::get('get-sign', 'Api\Fans\SignInController@get_sign');
        Route::post('sign-in', 'Api\Fans\SignInController@signIn');
        Route::apiResource('tasks', 'Api\Fans\SignInController');
    });
    //个性定制
    Route::apiResource('activity/diys', 'Api\Activities\DiyAcitvityController');
    //活动
    Route::apiResource('activity/activitys', 'Api\Activities\ActivityController');

    //商城
    //参数档
    Route::get('mall-parameter', 'Api\Malls\MallNavController@getParameter');
    //分类
    Route::apiResource('mall-navs', 'Api\Malls\MallNavController');
    // 商品
    Route::apiResource('mall-goods', 'Api\Malls\MallGoodController');
    Route::post('mall-goods/{mall-good}', 'Api\Malls\MallGoodController@show')->name('mallgoods');
    // 轮播图
    Route::apiResource('mall-groups', 'Api\Malls\MallSwiperGroupController');
    Route::apiResource('mall-swipers', 'Api\Malls\MallSwiperController');
    // 公众号
    Route::group(['prefix' => 'mall'], function () {
        Route::get('members', 'Api\Malls\MallGoodController@getMemberGoods');
        Route::get('discounts', 'Api\Malls\MallGoodController@getDiscountGoods');
        Route::get('generals', 'Api\Malls\MallGoodController@getGeneralGoods');
        Route::get('hots', 'Api\Malls\MallGoodController@getMallHots');
        Route::get('swipers', 'Api\Malls\MallSwiperGroupController@getSwipers');
    });
});

Route::get('oauth', 'Api\Fans\FanController@oauth');
Route::get('oauth-callback', 'Api\Fans\FanController@oauthCallback');

Route::group(['middleware' => ['token']], function () {
    Route::get('wechat', function () {
        return 'wechat';
    });
});


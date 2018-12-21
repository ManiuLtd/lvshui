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
Route::post('/login', 'Api\LoginController@login')->middleware(['cors']);

Route::group(['middleware' => ['cors', 'token']], function () {

    Route::post('qiniu/upload', 'Controller@upload');  //上传图片
    Route::post('qiniu/delete', 'Controller@delete');   //删除图片


    Route::get('get-uid', 'Api\Fans\FanController@getUid');
    Route::get('user', 'Api\Fans\FanController@getUser');
    Route::post('wechat/verify', 'Api\Fans\FanController@verifyToken'); //验证Token

    Route::apiResource('admins', 'Api\Fans\AdminController');

    Route::group(['prefix' => 'member'], function () {
        //会员卡
        Route::get('/members/{member}/group/{group_id}', 'Api\Members\MemberController@group');
        Route::post('members/change-integral', 'Api\Members\MemberController@changeIntegral');
        Route::post('/members/change-money', 'Api\Members\MemberController@changeMoney');
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
        //会员等级
        Route::get('/groups/{group_id}/default', 'Api\Members\GroupController@default');
        Route::apiResource('/groups', 'Api\Members\GroupController');

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
    Route::group(['prefix' => 'activity'], function () {
        //个性定制
        Route::post('diys/sign/{diy}', 'Api\Activities\DiyAcitvityController@sign');
        Route::apiResource('diys', 'Api\Activities\DiyAcitvityController');
        //活动
        Route::get('activitys/wx', 'Api\Activities\ActivitySignController@index');
        Route::post('activitys/wx/{activity}', 'Api\Activities\ActivitySignController@show');
        Route::apiResource('activitys', 'Api\Activities\ActivityController');
    });

    Route::group(['prefix' => 'share'], function () {
        Route::post('follow', 'Api\Fans\ShareController@share');
        Route::post('wx/show', 'Api\Fans\ShareController@shareShow');
        Route::post('wx/beshow', 'Api\Fans\ShareController@beShareShow');
        Route::group(['prefix' => 'over'], function () {
            Route::post('show', 'Api\Fans\ShareController@showRegister');
            Route::post('register', 'Api\Fans\ShareController@register');
            Route::post('check-register', 'Api\Fans\ShareController@checkRegister');
        });
        Route::apiResource('tasks', 'Api\Fans\ShareController');
    });

    Route::group(['prefix'=>'lottery'],function (){
       Route::post('fan/add','Api\Lotteries\ActivityController@addFanLottery');
       Route::get('fan/activity/{activity}','Api\Lotteries\ActivityController@wxShow');
       Route::post('result','Api\Lotteries\PrizeController@result');
       Route::apiResource('activitys','Api\Lotteries\ActivityController');
       Route::get('prizes/{activity}','Api\Lotteries\PrizeController@index');
       Route::apiResource('prizes','Api\Lotteries\PrizeController');
    });

    //商城
    //参数档
    Route::get('mall-parameter', 'Api\Malls\MallNavController@getParameter');
    //分类
    Route::apiResource('mall-navs', 'Api\Malls\MallNavController');
    // 商品
    Route::post('mall-goods/{good}/change', 'Api\Malls\MallGoodController@change');
    Route::apiResource('mall-goods', 'Api\Malls\MallGoodController');
    // 轮播图
    Route::put('mall-groups/{group}/change', 'Api\Malls\MallSwiperGroupController@change');
    Route::apiResource('mall-groups', 'Api\Malls\MallSwiperGroupController');
    Route::apiResource('mall-swipers', 'Api\Malls\MallSwiperController');
    // 公众号
    Route::group(['prefix' => 'mall'], function () {
        Route::get('members', 'Api\Malls\MallGoodController@getMemberGoods');
        Route::get('discounts', 'Api\Malls\MallGoodController@getDiscountGoods');
        Route::get('generals', 'Api\Malls\MallGoodController@getGeneralGoods');
        Route::get('groups', 'Api\Malls\MallGoodController@getGroupGoods');
        Route::get('hots', 'Api\Malls\MallGoodController@getMallHots');
        Route::get('swipers', 'Api\Malls\MallSwiperGroupController@getSwipers');
        Route::post('cart', 'Api\Orders\OrderController@cartVerify');
        Route::get('nav/{nav_id}', 'Api\Malls\MallNavController@getNavWithGood');
//        获取用户订单
        Route::get('orders', 'Api\Orders\OrderController@getFanOrder');
//        积分设置
        Route::apiResource('settings', 'Api\Malls\MallSettingController');
//        团购
        Route::post('group/opens','Api\Malls\MallGoodGroupController@store');
        Route::post('group/adds','Api\Malls\MallGoodGroupController@add');
        Route::post('group/open/sucess','Api\Malls\MallGoodGroupController@storeSucess');
        Route::post('group/add/sucess','Api\Malls\MallGoodGroupController@addSucess');

    });

    Route::group(['prefix' => 'order'], function () {
//        保存订单
        Route::apiResource('orders', 'Api\Orders\OrderController');
//        获取所有商城订单
        Route::get('malls', 'Api\Orders\OrderController@getMallOrder');
//        获取所有活动订单
        Route::get('actives', 'Api\Orders\OrderController@getAcitveOrder');
//        获取所有开通会员订单
        Route::get('joins', 'Api\Orders\OrderController@getJoinOrder');
//        获取所有退款订单
        Route::get('refunds', 'Api\Orders\OrderController@getRefundOrder');
//        获取最新订单截止日
        Route::get('settings/new', 'Api\Orders\OrderSettingController@getOrderSetting');
//        订单截止日
        Route::apiResource('settings', 'Api\Orders\OrderSettingController');
//        使用
        Route::post('uses', 'Api\Orders\OrderController@use');
    });

    Route::group(['prefix' => 'wechat'], function () {
        Route::any('server', 'Api\Wechat\OfficialAccountController@server');
        Route::any('config', 'Api\Wechat\OfficialAccountController@getConfig');
        Route::any('menu/create', 'Api\Wechat\OfficialAccountController@menuCreate');
        Route::any('menu/list', 'Api\Wechat\OfficialAccountController@menuList');
        Route::any('menu/delete', 'Api\Wechat\OfficialAccountController@menuDelete');
        Route::any('material/list','Api\Wechat\OfficialAccountController@getMaterialList');    
        Route::any('pay', 'Api\Wechat\PayController@pay');
        Route::any('refund', 'Api\Wechat\PayController@refund');
    });
});

Route::group(['prefix' => 'wechat'], function () {
    Route::any('oauth', 'Api\Wechat\OfficialAccountController@oauth');
    Route::any('oauth-callback', 'Api\Wechat\OfficialAccountController@oauthCallback');
    Route::any('pay-notify', 'Api\Wechat\PayController@notify');
});


Route::group(['middleware' => ['token']], function () {
    Route::get('wechat', function () {
        return 'wechat';
    });
});


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
Route::post('/login','Api\LoginController@login')->middleware(['cors']);

Route::group(['middleware' => ['cors', 'token']], function () {

    Route::post('qiniu/upload', 'Controller@upload');  //上传图片
    Route::post('qiniu/delete', 'Controller@delete');   //删除图片


    Route::get('get-uid','Api\Fans\FanController@getUid');
    Route::get('user','Api\Fans\FanController@getUser');
    Route::post('wechat/verify', 'Api\Fans\FanController@verifyToken'); //验证Token

    Route::apiResource('admins','Api\Fans\AdminController');

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
    Route::group(['prefix' => 'activity'], function () {
        //个性定制
        Route::post('diys/sign/{diy}','Api\Activities\DiyAcitvityController@sign');
        Route::apiResource('diys', 'Api\Activities\DiyAcitvityController');
        //活动
        Route::get('activitys/wx','Api\Activities\ActivitySignController@index');
        Route::post('activitys/wx/{activity}','Api\Activities\ActivitySignController@show');
        Route::apiResource('activitys', 'Api\Activities\ActivityController');
    });

    Route::group(['prefix'=>'share'],function(){
       Route::post('follow','Api\Fans\ShareController@share');
       Route::post('wx/show','Api\Fans\ShareController@shareShow');
        Route::post('wx/beshow','Api\Fans\ShareController@beShareShow');
       Route::group(['prefix'=>'over'],function (){
         Route::post('show','Api\Fans\ShareController@showRegister');
         Route::post('register','Api\Fans\ShareController@register');
         Route::post('check-register','Api\Fans\ShareController@checkRegister');
       });
       Route::apiResource('tasks','Api\Fans\ShareController');
    });

    //商城
    //参数档
    Route::get('mall-parameter', 'Api\Malls\MallNavController@getParameter');
    //分类
    Route::apiResource('mall-navs', 'Api\Malls\MallNavController');
    // 商品
    Route::post('mall-goods/{good}/change','Api\Malls\MallGoodController@change');
    Route::apiResource('mall-goods', 'Api\Malls\MallGoodController');
    // 轮播图
    Route::put('mall-groups/{group}/change','Api\Malls\MallSwiperGroupController@change');
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

Route::group(['prefix'=>'wechat'], function () {
    Route::get('oauth', 'Api\Fans\FanController@oauth');
    Route::get('oauth-callback', 'Api\Fans\FanController@oauthCallback');
    Route::get('config','Api\Fans\FanController@getConfig');        
    Route::get('pay', 'Api\Wechat\PayController@pay');
    Route::get('refund', 'Api\Wechat\PayController@refund');
    Route::get('pay-notify', 'Api\Wechat\PayController@notify');
});

Route::group(['middleware' => ['token']], function () {
    Route::get('wechat', function () {
        return 'wechat';
    });
});

Route::get('wechat-server', function() {

    $app = EasyWeChat\Factory::officialAccount(config('wechat.official_account.default'));

    $app->server->push(function ($message) {
        // $message['FromUserName'] // 用户的 openid
        // $message['MsgType'] // 消息类型：event, text....
        return "您好！欢迎使用 EasyWeChat";
    });

    $response = $app->server->serve();

    return $response;
});





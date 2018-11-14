<?php

namespace App\Http\Controllers\Api\Fans;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FanController extends Controller
{
    public function oauth()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        return $app->oauth->redirect();
    }

    public function oauthCallback() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user()->getOriginal()->toArray();
        $user['privilege'] = json_encode($user['privilege']);
        $token = \App\Services\officialAccountToken::getToken($user);
        return response()->json(['token' => $token]);
    }

    public function verifyToken() 
    {
        return response()->json(['isValid' => Token::verifyToken(request()->header('token'))]);
    }
}

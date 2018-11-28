<?php

namespace App\Http\Controllers\Api\Fans;

use App\Models\Fan;
use App\Services\Token;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\officialAccountToken;

class FanController extends Controller
{
    public function oauth(Request $request)
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        session(['url' => $request->url]);
        return $app->oauth->redirect();
    }

    public function oauthCallback() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user()->getOriginal();
        $user['privilege'] = json_encode($user['privilege']);

        $officialAccountToken = new officialAccountToken();

        $token = $officialAccountToken->getToken($user);
        
        $url = session('url').'?token='.$token;
        return redirect($url);
        // return view('redirect', ['url' => $url]);
    }

    public function verifyToken() 
    {
        return response()->json(['isValid' => Token::verifyToken(request()->header('token'))]);
    }

    public function getUid()
    {
        return response()->json(['fan_id' => Token::getUid()]);
    }

    public function getBasicConfig() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $jssdk = $app->jssdk->buildConfig(array('updateAppMessageShareData', 'updateTimelineShareData'), true); 
        $fan = Fan::find(Token::getUid());
        return response()->json(['data' => $fan, 'jssdk' => $jssdk]);

    }
}

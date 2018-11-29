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

        $subscribe = $app->user->get($user['openid']);
        $user['subscribe'] = $subscribe['subscribe'];
        $user['subscribe_time'] = $subscribe['subscribe_time'] ?? null;
        $user['privilege'] = json_encode($user['privilege']);
        dd($user);

        $officialAccountToken = new officialAccountToken();

        $token = $officialAccountToken->getToken($user);
        
        $baseUrl = session('url');

        if(strpos($baseUrl,'?') !== false) {
            $url = $baseUrl.'&token='.$token;
        } else {
            $url = $baseUrl.'?token='.$token;
        }
        session(['url' => $url]);
        return redirect($url);
    }

    public function verifyToken() 
    {
        return response()->json(['isValid' => Token::verifyToken(request()->header('token'))]);
    }

    public function getUid()
    {
        return response()->json(['fan_id' => Token::getUid()]);
    }

    public function getConfig() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $app->jssdk->setUrl(session('url'));
        $jssdk = $app->jssdk->buildConfig(array('onMenuShareTimeline','onMenuShareAppMessage','updateAppMessageShareData', 'updateTimelineShareData'), false,false, false); 
        return response()->json(['jssdk' => $jssdk]);
    }

    public function getUser()
    {
        $fan = Fan::with('admin')->find(Token::getUid());
        return response()->json(['data' => $fan]);
    }
}

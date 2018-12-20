<?php

namespace App\Http\Controllers\Api\Wechat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Handler\TextMessageHandler;
use App\Handler\EventMessageHandler;
use App\Http\Controllers\Controller;
use App\Services\OfficialAccountToken;
use EasyWeChat\Kernel\Messages\Message;


class OfficialAccountController extends Controller
{

    public function server() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $app->server->push(TextMessageHandler::class, Message::TEXT);
        $app->server->push(EventMessageHandler::class, Message::EVENT);
        $response = $app->server->serve();
    
        return $response;
    }

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

        $officialAccountToken = new OfficialAccountToken();

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

    public function getConfig(Request $request) 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $url = $request->header('url') ?? session('url');
        $app->jssdk->setUrl($url);
        $jssdk = $app->jssdk->buildConfig(array('onMenuShareTimeline','onMenuShareAppMessage','updateAppMessageShareData', 'updateTimelineShareData'), false,false, false); 
        return response()->json(['jssdk' => $jssdk]);
    }

    public function menuCreate() 
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $menu = request('menu');
        $res = $app->menu->create($menu);
        return $res;
    }

    public function menuList()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $list = $app->menu->list();
        return response()->json(['data' => $list]);
    }

    public function menuDelete()
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $menuId = request('menuId');
        $res = $app->menu->delete($menuId);
        return $res;
    }

    public function getMaterialList() {
        $type = request('type') ?? 'news';
        $count = 20;
        $page = request('page') ?? 1;
        $offset = ($page - 1) * $count;
        $app = Factory::officialAccount(config('wechat.official_account.default'));        
        $material = $app->material->list($type, $offset, $count);
        return response()->json(['data' => $material]);
    }
}
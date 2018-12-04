<?php

namespace App\Services;

use Exception;
use App\Models\Fan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OfficialAccountToken extends Token
{
    public function getToken(array $data) : string
    {
        $openid = $data['openid'];
        $unionid = $data['unionid'] ?? '';
        $fans = Fan::getByOpenID($openid);
        if (!$fans)
        {
            $uid = $this->newUser($data);
        }else {
            $fans->where('id', $fans->id)->update($data);
            $uid = $fans->id;
        }
        $cachedValue = $this->prepareCachedValue($data, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function prepareCachedValue(array $data, int $uid) : array
    {
        $cachedValue = $data;
        $cachedValue['uid'] = $uid;
        // $cachedValue['scope'] = \App\Utils\RoleScope::User;
        return $cachedValue;
    }

    private function saveToCache(array $values) : string
    {

        $token = self::generateToken();
        $expire_in = config('token.token_expire_in') ?? 120;
        Cache::put($token, json_encode($values), $expire_in);
        if(!Cache::get($token)){
            return false;
        }
        return $token;
    }

    // 创建新用户
    private function newUser(array $user) : int
    {
        $user = Fan::create($user);
        return $user->id;
    }
}
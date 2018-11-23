<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;


class LoginController extends Controller
{
    public function login(){
        //token认证账号
        $client = new \App\Services\ClientToken();
        $token = $client->getToken(request('username'), request('password'));
        if(is_string($token)) {
            $user_id = \Auth::guard('users')->id();
            return response()->json(['status' => 'success', 'token' => $token, 'msg' => '登录成功！','user'=>['id' => $user_id, 'username' => request('username')]]);
        }else{
            return response()->json(['status' => 'error', 'msg' => '用户名或者密码错误！']);
        }
    }

}
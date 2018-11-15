<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Services\WechatPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    public function pay()
    {
        $pay = WechatPay::getApp();
        $order = [
            'body' => '任意门支付测试',
            'out_trade_no' => 5833837456292782,
            'total_fee' => 0.01 * 100,
            'trade_type' => 'JSAPI',
            'openid' => 'oLOcY0jf0SLhG_LN27yU0FIZJWUo',
        ];

        $payment = $pay->pay($order);

        dd($payment);
    }
}

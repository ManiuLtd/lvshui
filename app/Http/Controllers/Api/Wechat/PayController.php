<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Services\WechatPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    public function pay()
    {
        
        $order = [
            'body' => '任意门支付测试',
            'out_trade_no' => \App\Utils\Common::generateOrderNo(),
            'total_fee' => 0.01 * 100,
            'trade_type' => 'JSAPI',
            'openid' => 'oLOcY0jf0SLhG_LN27yU0FIZJWUo',
        ];

        $payment = WechatPay::pay($order);
        
        return response()->json(['payment' => $payment]);    
    }

    public function refund() 
    {
        $payment = WechatPay::refund($order);

    }
}

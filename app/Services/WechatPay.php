<?php

namespace App\Services;

use EasyWeChat\Factory;
use App\Models\Wechat\Pay;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class WechatPay extends Model
{
    private $app;

    public function __construct() 
    {
        $config = config('wechat.payment.default');

        $this->app = Factory::payment($config);
    }

    // public static function getApp()
    // {
    //     $config = config('wechat.payment.default');

    //     $app = Factory::payment($config);

    //     return $app;
    // }

    public static function unify($order)
    {
        // $result = $app->order->unify([
        //     'body' => $order->body,
        //     'out_trade_no' => $order->order_no,
        //     'total_fee' => $order->price * 100,
        //     'trade_type' => 'JSAPI',
        //     'openid' => $order->fan->openid,
        // ]);
        $result = $this->app->order->unify($order);

        $prepay_id = $result['prepay_id'];
        
        $payment =  $this->app->jssdk->bridgeConfig($prepay_id, false);

        return $payment;
    }

    public function refund($order, $desc = '取消订单') 
    {
        $result = $this->app->refund->byTransactionId($order->trans_no, 'TK'.$order->order_no, $order->price * 100, $order->price * 100, [
            // 可在此处传入其他参数，详细参数见微信支付文档
            'refund_desc' => $desc,
        ]);

        return $result;
    }
    

    
    
}

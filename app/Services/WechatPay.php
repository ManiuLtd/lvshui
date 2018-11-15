<?php

namespace App\Services;

use EasyWeChat\Factory;
use App\Models\Wechat\Pay;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class WechatPay extends Model
{
    public function getApp()
    {
        $config = config('wechat.payment.default');

        $app = Factory::payment($config);

        return $app;
    }

    public function pay($order)
    {
        $app = $this->getApp($order->xcx_id);

        // $result = $app->order->unify([
        //     'body' => $order->body,
        //     'out_trade_no' => $order->order_no,
        //     'total_fee' => $order->price * 100,
        //     'trade_type' => 'JSAPI',
        //     'openid' => $order->openid,
        // ]);
        $result = $app->order->unify($order);

        $prepay_id = $result['prepay_id'];
        
        $payment =  $app->jssdk->bridgeConfig($prepay_id, false);

        return array_merge($payment,['prepay_id' => $prepay_id]);
    }

    public function refund($order, $desc = '取消订单') 
    {
        $app = $this->getApp($order->xcx_id);
        $result = $app->refund->byTransactionId($order->trans_no, 'TK'.$order->order_no, $order->price * 100, $order->price * 100, [
            // 可在此处传入其他参数，详细参数见微信支付文档
            'refund_desc' => $desc,
        ]);

        return $result;
    }
    

    
    
}

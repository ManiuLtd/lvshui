<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Services\WechatPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    public function unify()
    {
        //TODO: 获取订单信息
        $order = [
            'body' => '任意门支付测试',
            'out_trade_no' => \App\Utils\Common::generateOrderNo(),
            'total_fee' => 1 * 100,
            'trade_type' => 'JSAPI',
            'openid' => 'o_FNZ1OcVWhKprNi2LjuOP-5DyAc',
        ];

        $payment = WechatPay::unify($order);
        
        return response()->json(['payment' => $payment]);    
    }

    public function refund() 
    {
        //TODO: 获取订单信息
        
        $result = WechatPay::refund($order);

        if($result['result_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            if($order->update(['status' => OrderStatus::REFUND_SUCCESS])){
                return response()->json(['status' => 'success', 'msg' => '退款成功！']);         
            }
        }else {
            return response()->json(['status' => 'error', 'msg' => $result['err_code_des']]);  
        }  

    }

    public function notify() 
    {

        $app = WechatPay::getApp();

        $response = $app->handlePaidNotify(function($message, $fail) use ($xcx_id){

            $order = Order::where('order_no', $message['out_trade_no'])->first();
        
            if (!$order || $order->pay_time) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
        
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $order->pay_time = date('Y-m-d H:i:s', time()); // 更新支付时间为当前时间
                    $order->trans_no = $message['transaction_id']; // 更新支付时间为当前时间
                    $order->status = OrderStatus::PAID;
                    $order->save();
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
        
            return true; // 返回处理完成
        });
        

        return $response;
    }
}

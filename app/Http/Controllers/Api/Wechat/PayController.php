<?php

namespace App\Http\Controllers\Api\Wechat;

use App\Models\Activity;
use App\Models\Fan;
use App\Models\FanTicket;
use App\Models\MallSetting;
use App\Models\Order;
use App\Models\OrderSetting;
use App\Models\Ticket;
use App\Services\Token;
use App\Services\WechatPay;
use App\Utils\Parameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{
    public function unify()
    {
        //TODO: 获取订单信息
        $id = request('id');
        $order = Order::find($id);
        $order->fan = Fan::find($order->fan_id);
        $order = [
            'body' => $order->body,
            'out_trade_no' => $order->order_no,
            'total_fee' => $order->price * 100,
            'trade_type' => 'JSAPI',
            'openid' => $order->fan->openid,
        ];

        $payment = WechatPay::unify($order);
        
        return response()->json(['payment' => $payment]);    
    }

    public function refund() 
    {
        //TODO: 获取订单信息
        $id = request('id');
        $order = Order::find($id);

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
        \Log::info('支付完成');
        
        $app = WechatPay::getApp();

        $response = $app->handlePaidNotify(function($message, $fail) {

            $order = Order::where('order_no', $message['out_trade_no'])->with('orderGoods')->first();

            if (!$order || $order->pay_time) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    \Log::info("支付成功执行回调");
                    $order->pay_time = date('Y-m-d H:i:s', time()); // 更新支付时间为当前时间
                    $order->trans_no = $message['transaction_id'];
                    $order->pay_state = 1;
                    if($order->type = Parameter::mall){

                        \Log::info("支付：商城");

                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;
                        $integral = 0;
                        // 积分处理
                        $mallSetting = MallSetting::first();
                        if ($mallSetting) {
                            \Log::info("支付：积分处理存在");
                            $switch = $mallSetting->switch;
                            if ($switch == 1) {
                                $radio = $mallSetting->radio;
                                $price = $order->price;
                                $integral = round($price * $radio); //积分
                            }
                        }
                        $order->integral = $integral;
                        //截止日
                        \Log::info("支付：截止日");
                        $orderSettings = OrderSetting::where('switch', 1)->first();
                        $order->end_id = $orderSettings->id;
                        if ($orderSettings->type = 'date') {
                            $order->end_date = $orderSettings->date;
                        } else {
                            $day = $orderSettings->day;
                            $order->end_date = Carbon::tomorrow(Carbon::now()->addDays($day));
                        }
                        \Log::info("支付".$order);
                        DB::beginTransaction();
                        try {
                            Order::where('id', $order->id)->update($order);
                            //Fans表 积分处理
                            Fan::where('id',$order->fan_id)->increment('point',$integral);
                            DB::commit();
                            \Log::info("支付成功");
                        } catch (\Exception $e) {
                            DB::rollBack();
                            \Log::info("支付失败：". $e);
                        }

                    }else if($order->type = Parameter::active){
                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;

                        $goods = $order->orderGoods;
                        $active = Activity::find($goods[0]->good_id);
                        $order->end_date = $active->end_time;
                        $order->save();

                    }else if($order->type = Parameter::ticket){
                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;

                        $goods = $order->orderGoods;

                        $ticket = FanTicket::find($goods[0]->good_id);

                        $order->end_date = $ticket->booking_date;
                        $order->save();

                    }else if($order->type = Parameter::join){
                        $order->save();
                    }

                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            return true; // 返回处理完成
        });


        return $response;
    }

    public function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz 
               ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key ="";
        for ($i = 0; $i < $length; $i++) {
            //生成php随机数
            $key .= $pattern{mt_rand(0, 35)};
        }
        return $key;
    }
}

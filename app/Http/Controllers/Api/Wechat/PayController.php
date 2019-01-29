<?php

namespace App\Http\Controllers\Api\Wechat;

use Carbon\Carbon;
use App\Models\Fan;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\Token;
use App\Models\Activity;
use App\Models\MallGood;
use App\Utils\Parameter;
use App\Models\FanTicket;
use App\Models\MallSetting;
use App\Services\WechatPay;
use App\Models\OrderSetting;
use Illuminate\Http\Request;
use App\Services\TemplateNotice;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    public function unify()
    {
        //TODO: 获取订单信息
        $id = request('id');
        $order = Order::find($id);
        $openid = Fan::find($order->fan_id)['openid'];

        $order = [
            'body' => $order->body,
            'out_trade_no' => $order->order_no,
            'total_fee' => $order->price * 100,
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ];

        $payment = WechatPay::unify($order);
        
        return response()->json(['payment' => $payment]);    
    }

    public function refund() 
    {
        //TODO: 获取订单信息
        $id = request('id');
        $order = Order::where('id', $id)->with('orderGoods')->first();
        $oGoods = $order->orderGoods;
        $result = WechatPay::refund($order);

        if($result['result_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            DB::beginTransaction();
            try {
                $order->update([ 'use_state'=> -2 ,
                                 'refund_time'=> date('Y-m-d H:i:s', time()) ]);
                if($order->type = Parameter::mall){
                    Fan::where('id', $order->fan_id)->decrement('point', $order->integral);
                    foreach ($oGoods as $oGood) {
                        MallGood::where([['id', $oGood->good_id], ['up_id', $oGood->up_id]])->increment('stock', $oGood->num);
                    }
                }else if($order->type = Parameter::ticket){

                }
                DB::commit();
                return response()->json(['status' => 'success', 'msg' => '退款成功！']);
            } catch (\Exception $e) {
                DB::rollBack();
            }

        }else {
            return response()->json(['status' => 'error', 'msg' => $result['err_code_des']]);  
        }  

    }

    public function notify() 
    {
        $app = WechatPay::getApp();

        $response = $app->handlePaidNotify(function($message, $fail) {

            $order = Order::where('order_no', $message['out_trade_no'])->with('orderGoods')->first();

            if (!$order || $order->pay_time) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $order->pay_time = date('Y-m-d H:i:s', time()); // 更新支付时间为当前时间
                    $order->trans_no = $message['transaction_id'];
                    $order->pay_state = 1;
                    if($order->type == Parameter::mall){

                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;

                        $integral = 0;
                        // 积分处理
                        $mallSetting = MallSetting::first();
                        if ($mallSetting) {
                            $switch = $mallSetting->switch;
                            if ($switch == 1) {
                                $radio = $mallSetting->radio;
                                $price = $order->price;
                                $integral = round($price * $radio); //积分
                            }
                        }
                        $order->integral = $integral;
                        //截止日
                        $orderSettings = OrderSetting::where('switch', 1)->first();
                        $order->end_id = $orderSettings->id;
                        if ($orderSettings->type = 'date') {
                            $order->end_date = $orderSettings->date;
                        } else {
                            $day = $orderSettings->day;
                            $order->end_date = Carbon::now()->addDays($day);
                        }
                        DB::beginTransaction();
                        try {
                            Order::where('id', $order->id)->update([
                                'pay_state'=>$order->pay_state,'use_state'=>$order->use_state,
                                'pay_time'=>$order->pay_time,'trans_no'=>$order->trans_no,
                                'end_id'=>$order->end_id,'end_date'=>$order->end_date,
                                'integral'=>$order->integral,'discount_type'=>$order->discount_type,
                                'discount'=>$order->discount,'use_no'=>$order->use_no
                            ]);
                            //Fans表 积分处理
                            Fan::where('id',$order->fan_id)->increment('point',$integral);
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                        }

                    }else if($order->type == Parameter::active){
                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;

                        $goods = $order->orderGoods;
                        $active = Activity::find($goods[0]->good_id);
                        $order->end_date = $active->end_time;
                        $order->save();

                    }else if($order->type == Parameter::ticket){
                        \Log::info('门票支付');
                        $rand = $this->randomkeys(4);
                        $use_no = $order->order_no . $rand;
                        $order->use_no = $use_no;

                        $goods = $order->orderGoods;

                        $fanTicket = FanTicket::find($goods[0]->good_id);
                        $ticket = Ticket::find($fanTicket->ticket_id);
                        $fan = Fan::find($fanTicket->fan_id);
                        \Log::info($fan->name.':'.$fan->openid);

                        $order->end_date = $fanTicket->booking_date;
                        $order->save();

                        $template = new  TemplateNotice();
                        //通知用户
                        $fanArray = [
                            'first'=>'套餐已购买成功！',
                            'keyword1' => $ticket->name,
                            'keyword2' => $order->order_no,
                            'keyword3' => $order->price.'元',
                            'keyword4' => $order->pay_time,
                            'remark' => '温馨提示：请在预定日期 '. $fanTicket->booking_date .' 当天使用'
                        ];
                        $template->sendNotice($fan->openid,'HZ8pJsXjhakqwtQQw1ijgTlbizfdHJUbZqsWE3HGepw',
                        'zhlsqj.com/#/share',$fanArray);

                        //通知管理员
                        $admins = Admin::with('fan')->get();
                        $adminArray = [
                            'first'=>$ticket->name.' 套餐成功售出！请留意！',
                            'keyword1' => $fanTicket->mobile,
                            'keyword2' => $order->price.'元',
                            'keyword3' => $order->order_no,
                            'keyword4' => $order->pay_time,
                            'remark' => '预约日期：'. $fanTicket->booking_date 
                        ];
                        foreach($admins as $admin) {
                            $template->sendNotice($admin->fan->openid,
                            'f2F3iCL9fCZkyxqvXY8nMl_BI1QFF_bS-uIUeThpIQs',
                        'zhlsqj.com/#/share',$adminArray);
                        }
                        

                    }else if($order->type == Parameter::join){
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

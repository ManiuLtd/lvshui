<?php

namespace App\Http\Controllers\Api\Orders;

use App\Models\Activity;
use App\Models\Fan;
use App\Models\FanTicket;
use App\Models\JoinSetting;
use App\Models\MallGood;
use App\models\MallGoodGroup;
use App\Models\MallSetting;
use App\Models\Member;
use App\Models\MemberSetting;
use App\Models\Order;
use App\Models\OrderGood;
use App\Models\OrderSetting;
use App\Models\SignHistory;
use App\Models\Ticket;
use App\Services\Token;
use App\Utils\Parameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Param;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

//退货、退款
class OrderController extends Controller
{
//    获取商城订单
    public function getMallOrder()
    {
        $pay_state = request('pay_state');
        $use_state = request('use_state');
        if ($pay_state == 0) {
            $orders = Order::where([['type', Parameter::mall], ['pay_state', 0]])
                ->orderBy('created_at', 'desc')
                ->with(['goods' => function ($query) {
                    $query->with('imgs');
                }])
                ->with('setting')
                ->paginate(20);
            return response()->json(['data' => $orders]);
        } else if ($pay_state == 1) {
            if ($use_state) {
                $orders = Order::where([['type', Parameter::mall], ['pay_state', 1], ['use_state', $use_state]])
                    ->orderBy('created_at', 'desc')
                    ->with(['goods' => function ($query) {
                        $query->with('imgs');
                    }])
                    ->with('setting')
                    ->paginate(20);
                return response()->json(['data' => $orders]);
            } else {
                $orders = Order::where([['type', Parameter::mall], ['pay_state', 1]])
                    ->orderBy('created_at', 'desc')
                    ->with(['goods' => function ($query) {
                        $query->with('imgs');
                    }])
                    ->with('setting')
                    ->paginate(20);
                return response()->json(['data' => $orders]);
            }

        }
    }

//   获取活动订单
    public function getAcitveOrder()
    {
        $orders = Order::where('type', Parameter::active)->orderBy('created_at', 'desc')->with('active')->paginate(20);
        return response()->json(['data' => $orders]);
    }

//   获取会员订单
    public function getJoinOrder()
    {
        $orders = Order::where('type', Parameter::join)->orderBy('created_at', 'desc')->with('join')->paginate(20);
        return response()->json(['data' => $orders]);
    }

//    获取门票订单
    public function getTicketOrder()
    {
        $orders = Order::where('type',Parameter::ticket)
            ->orderBy('created_at', 'desc')
            ->with(['fanTicket' => function ($query) {
                $query->with('ticket');
            }])->paginate(20);
        return response()->json(['data' => $orders]);
    }

//    获取订单
    public function getOrder()
    {
        $list = request(['pay_state', 'use_state', 'type']);
        $orders = Order::where([
            ['pay_state', $list['pay_state']],
            ['type', $list['type']],
            ['use_state', $list['use_state']]

        ])
            ->orderBy('created_at', 'desc')
            ->when($list['type'] == Parameter::mall, function ($query) {
                $query->with(['goods' => function ($query) {
                    $query->with('imgs');
                }]);
            })
            ->when($list['type'] == Parameter::active, function ($query) {
                $query->with('active');
            })
            ->when($list['type'] == Parameter::join, function ($query) {
                $query->with('join');
            })
            ->when($list['type'] == Parameter::ticket, function ($query) {
                $query->with(['fanTicket' => function ($query) {
                    $query->with('ticket');
                }]);
            })
            ->with('orderGoods')
            ->with('setting')
            ->paginate(20);
        return response()->json(['data' => $orders]);
    }

//   获取申请退款订单
    public function getRefundOrder()
    {
        $order = Order::where('use_state', -1)->orderBy('created_at', 'desc')->get();
        $mall = $order->where('type', Parameter::mall)->get();
        $active = $order->where('type', Parameter::active)->get();
        return response()->json(['all' => $order, '$mall' => $mall, 'active' => $active]);
    }

//   获取用户所有商城订单
    public function getFanOrder()
    {
        $fan_id = Token::getUid();
        $orders = Order::where('fan_id', $fan_id)
            ->orderBy('created_at', 'desc')
            ->with(['goods' => function ($query) {
                $query->with('imgs');
            }])
            ->with('orderGoods')
            ->with('setting')
            ->paginate(20);
        return response()->json(['data' => $orders]);
    }

//    依类型获取当前订单
    public function getFanOrderByState()
    {
        $fan_id = Token::getUid();
        $list = request(['pay_state', 'use_state', 'type']);

        $orders = Order::where([
            ['fan_id', $fan_id],
            ['pay_state', $list['pay_state']],
            ['type', $list['type']],
            ['use_state', $list['use_state']]

        ])
            ->orderBy('created_at', 'desc')
            ->when($list['type'] == Parameter::mall, function ($query) {
                $query->with(['goods' => function ($query) {
                    $query->with('imgs');
                }]);
            })
            ->when($list['type'] == Parameter::active, function ($query) {
                $query->with('active');
            })
            ->when($list['type'] == Parameter::join, function ($query) {
                $query->with('join');
            })
            ->when($list['type'] == Parameter::ticket, function ($query) {
                $query->with(['fanTicket' => function ($query) {
                    $query->with('ticket');
                }]);
            })
            ->with('orderGoods')
            ->with('setting')
            ->paginate(20);
        return response()->json(['data' => $orders]);
    }
//    依照类型获取用户某一类型所有订单
    public function getFanOrderByType()
    {
        $fan_id = Token::getUid();
        $type = request('type');
        $orders = Order::where([
            ['fan_id', $fan_id],
            ['type', $type],
        ])
            ->orderBy('created_at', 'desc')
            ->when($type == Parameter::mall, function ($query) {
                $query->with(['goods' => function ($query) {
                    $query->with('imgs');
                }]);
            })
            ->when($type == Parameter::active, function ($query) {
                $query->with('active');
            })
            ->when($type == Parameter::join, function ($query) {
                $query->with('join');
            })
            ->when($type == Parameter::ticket, function ($query) {
                $query->with(['fanTicket' => function ($query) {
                    $query->with('ticket');
                }]);
            })
            ->with('orderGoods')
            ->with('setting')
            ->paginate(20);
        return response()->json(['data' => $orders]);
    }



//   获取单笔订单
    public function showOrder()
    {
        $id = request()->order;
        $type = request('type');
        $order = Order::where('id', $id)
            ->when($type == Parameter::mall, function ($query) {
                $query->with(['goods' => function ($query) {
                    $query->with('imgs');
                }]);
            })
            ->when($type == Parameter::ticket, function ($query) {
                $query->with(['fanTicket' => function ($query) {
                    $query->with('ticket');
                }]);
            })->with('orderGoods')
            ->with('setting')
            ->get();
        return response()->json(['data' => $order]);
    }

//  购物车验证
    public function cartVerify()
    {
        $rGoods = request('goods');
        $fan_id = Token::getUid();
        $member = Member::find($fan_id); //会员
        $rIDs = []; // id集合
        $data = []; //
        foreach ($rGoods as $rGood) {
            array_push($rIDs, $rGood['id']);
        }
        $goods = MallGood::whereIn('id', $rIDs)->with('imgs')->get();

        foreach ($rGoods as $rGood) {
            $good = $goods->where('id', $rGood['id'])->first();
            $price = $good->price;
            $good->error = 0; //无错误
            if ($good->limit != 0) {
                $orderGoods = OrderGood::where([['fan_id', $fan_id], ['up_id', $good->up_id]])->get();
                if (count($orderGoods) > $good->limit) {
                    $good->error = 6; //商品已达到购买上限
                }
            }

            if ($good->type == Parameter::member && $member == null) {
                $good->error = 1; //用户非会员 存在会员商品
            }

            if ($good->limit != 0 && $rGood['num'] > $good->limit) {
                $good->error = 2; //商品数量超出商品上限
            }

            if ($rGood['num'] > $good->stock) {
                $good->error = 3; //商品大于库存
            }

            if ($good->stock == 0) {
                $good->error = 4; //商品已售罄
            }

            if ($good->is_up == 0) {
                $good->error = 5; //商品已下架
            }

            if ($good->type == Parameter::general) {
                if ($member != null) {
                    $memberSet = MemberSetting::first();
                    $offer_status = $memberSet->offer_status;
                    $offers = json_decode($memberSet->offer);
                    if ($offer_status == 2) {
//                  折扣
                        $discount = $offers[0]->discount;
                        $good->endPrice = sprintf("%.2f", $price * $discount);
                    } else {
                        $good->endPrice = $price;
                    }
                } else {
                    $good->endPrice = $price;
                }
            } else {
                $good->endPrice = $good->discount;
            }
            $data[] = $good;
        }
        return response()->json(['data' => $data]);
    }

//  依类型订单保存
    public function store()
    {
        $data = request()->all();
        $result = 0;
        switch ($data['type']) {
            case Parameter::mall:
                $result = $this->orderMall($data['ps'], $data['goods']);
                break;
            case Parameter::active:
                $result = $this->orderActive($data['active_id']);
                break;
            case Parameter::join:
                $result = $this->orderJoin($data['join_id']);
                break;
            case Parameter::ticket;
                $result = $this->orderTicket($data['ticket_id'], $data['name'], $data['mobile'], $data['purchase_quantity'], $data['booking_date']);
                break;
        }
        return $result;
    }

//  商城订单
    public function orderMall($ps, array $rGoods)
    {
        $type = Parameter::mall;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-商城商品';
        $genealPrice = 0; //一般类型商品总价
        $price = 0; //总价
        $discount_type = 0; //一般类型商品优惠
        $pDiscount = 0;//普通商品
        $rgIDs = []; //商品id集合
        $member = Member::find($fan_id); //会员
        $is_error = 0;

        //提取商品id
        foreach ($rGoods as $rGood) {
            array_push($rgIDs, $rGood['id']);
        }
        //会员情况
        if ($member) {
            $memberSet = MemberSetting::first();
            $offer_status = $memberSet->offer_status;
            $offers = json_decode($memberSet->offer);
        }

        $goods = MallGood::whereIn('id', $rgIDs)->get(); //商品集合
        foreach ($rGoods as $rGood) {
            $good = $goods->where('id', $rGood['id'])->first();
            $gDiscount = $good->discount;
            $gPrice = $good->price;
            $num = $rGood['num'];
            $good->error = 0; //无错误

            //验证
            if ($good->type == Parameter::member && $member == null) {
                $good->error = 1; //用户非会员 存在会员商品
                $is_error = 1;

            }

            if ($good->limit != 0 && $num > $good->limit) {
                $good->error = 2; //商品数量超出商品上限
                $is_error = 1;
            }

            if ($num > $good->stock) {
                $good->error = 3; //商品大于库存
                $is_error = 1;
            }

            if ($good->stock == 0) {
                $good->error = 4; //商品已售罄
                $is_error = 1;
            }

            if ($good->is_up == 0) {
                $good->error = 5; //商品已下架
                $is_error = 1;
            }

            if ($good->limit != 0) {
                $orderGoods = OrderGood::where([
                    ['fan_id', $fan_id], ['up_id', $good->up_id]
                ])->get();
                if (count($orderGoods) > $good->limit) {
                    $good->error = 6; //商品已达到购买上限
                    $is_error = 1;
                }
            }

            if ($good->type != Parameter::general) {
                $price = $price + ($gDiscount * $num);
            } else {
                if ($member) {
                    //折扣
                    if ($offer_status == 2) {
                        $discount = $offers[0]->discount;
                        $discount_type = 2;
                        $pDiscount = $pDiscount + $gPrice - sprintf("%.2f", $gPrice * $discount);
                        $price = $price + sprintf("%.2f", $gPrice * $discount);
                    } else {
                        $genealPrice = $genealPrice + $gPrice;
                    }
                } else {
                    $genealPrice = $genealPrice + $gPrice;
                }
            }
        }
        if ($is_error == 1) {
            return response()->json(['status' => 0, 'data' => $goods]);
        }

        if ($member) {
            // 满减 从小到大
            if ($offer_status == 1) {
                $discount = 0;
                $count = count($offers);
                for ($i = 0; $i < $count; $i++) {
                    if ($i + 1 < $count) {
                        if ($genealPrice >= $offers[$i]->condition && $genealPrice < $offers[$i + 1]->condition) {
                            $discount = $offers[$i]->discount;
                            break;
                        }
                    } else {
                        $discount = $offers[$i]->discount;
                    }
                }
                $genealPrice = $genealPrice - $discount;
                $discount_type = 1;
                $pDiscount = $discount;
            }
        }
        $price = $price + $genealPrice;
        // 订单号
        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'ps' => $ps, 'order_no' => $order_no,
                'body' => $body, 'discount_type' => $discount_type, 'discount' => $pDiscount
            ]);
            foreach ($rGoods as $rGood) {
                $good = $goods->where('id', $rGood['id'])->first();
                OrderGood::create([
                    'type' => $good->type, 'order_id' => $order->id, 'good_id' => $rGood['id'], 'num' => $rGood['num'],
                    'price' => $good->price, 'discount' => $good->discount, 'fan_id' => $fan_id, 'up_id' => $good->up_id
                ]);
                MallGood::where('id', $rGood['id'])->update(['stock' => $good->stock - $rGood['num'], 'monthly_sales' => $good->monthly_sales + $rGood['num']]);
            }
            DB::commit();
            return response()->json(['status' => 1, 'msg' => '新增成功！', 'id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
    }

//  二维码使用
    public function use()
    {
        $today = Carbon::today();
        $qrcode = request('qrcode');
        $order = Order::where('use_no', $qrcode)->first();
        $order_setting = OrderSetting::find($order->end_id);
        $end_date = Carbon::parse($order_setting->end_date);

        if ($order->pay_state != 1) {
            return response()->json(['status' => 0, 'error' => 1, 'msg' => '该订单未支付或已取消']);
        }
        if ($order->use_state == 1) {
            return response()->json(['status' => 0, 'error' => 2, 'msg' => '该订单已被使用']);
        }
        if ($order->use_state == -1) {
            return response()->json(['status' => 0, 'error' => 3, 'msg' => '该订单正申请退款']);
        }
        if ($order->use_state == -2) {
            return response()->json(['status' => 0, 'error' => 4, 'msg' => '该订单已退款']);
        }
        if ($today->gt($end_date)) {
            return response()->json(['status' => 0, 'error' => 5, 'msg' => '该订单已过期']);
        }

        if ($order->use_state == 0 || $order->use_state == -3) {
            if ($order->pay_state == 1) {
                $order->use_state = 1;
                $order->use_time = Carbon::now();

                DB::beginTransaction();
                try {
                    Order::where('id', $order->id)->update($order->toArray());
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
                }
                return response()->json(['status' => 'success', 'msg' => '修改成功！']);
            }
        }
    }

//  生成php随机数
    public function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz 
               ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

    public function orderActive($active_id)
    {
        $type = Parameter::active;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-活动报名';
        $active = Activity::find($active_id);
        $sign_end_time = Carbon::parse($active->sign_end_time);
        if ($sign_end_time->lt(Carbon::now())) {
            return response()->json(['state' => 'error', 'message' => '活动报名已结束']);
        }
        $price = $active->sign_price;

        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'order_no' => $order_no, 'body' => $body]);

            OrderGood::create(['type' => $type, 'order_id' => $order->id, 'good_id' => $active_id, 'num' => 1, 'price' => $price]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function orderJoin($join_id)
    {
        $type = Parameter::join;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-开通会员';
        $join = JoinSetting::find($join_id);
        $price = $join->price;

        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'order_no' => $order_no, 'body' => $body]);

            OrderGood::create(['type' => $type, 'order_id' => $order->id, 'good_id' => $join_id, 'num' => 1, 'price' => $price]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function orderTicket($ticket_id, $name, $mobile, $purchase_quantity, $booking_date)
    {
        $type = Parameter::ticket;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-门票';
        $ticket = Ticket::find($ticket_id);
        $price = $ticket->price * $purchase_quantity;

        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'order_no' => $order_no, 'body' => $body]);

            $fanTicket = FanTicket::create([
                'fan_id' => $fan_id, 'ticket_id' => $ticket_id, 'name' => $name, 'mobile' => $mobile,
                'purchase_quantity' => $purchase_quantity, 'booking_date' => $booking_date

            ]);

            OrderGood::create([
                'type' => $type, 'order_id' => $order->id, 'good_id' => $fanTicket->id,
                'num' => $purchase_quantity, 'price' => $ticket->price
            ]);

            DB::commit();
            return response()->json(['status' => 1, 'msg' => '新增成功！', 'id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
    }

//    取消订单
    public function cancle()
    {
        $id = request()->order;
        Order::where('id', $id)->update(['pay_state' => -1]);
    }

//    申请退款
    public function applyRefund()
    {
        $id = request()->order;
        $order = Order::where('id', $id)->with('orderGoods')->first();
        // $oGoods = $order->orderGoods;
        // $fan_id = Token::getUid();
//        退款判断 ： 直接修改团购表状态
//        foreach ($oGoods as $oGood){
//            if($oGood->type == Parameter::group){
//                MallGoodGroup::where([['fan_id',$fan_id],['good_id',$oGood->good_id],['state',1]])->update(['state'=>0]);
//            }else{
//                break;
//            }
//        }
        Order::where([['id', $id], ['use_state', 0]])->update(['use_state' => -1]);
    }

//    拒绝退款
    public function declineRefund()
    {
        $id = request()->order;
        Order::where('id', $id)->update(['use_state' => -3]);
    }

//    退款成功
    public function refund()
    {
        $list = request(['id', 'refund_time']);
        $order = Order::where('id', $list['id'])->with('orderGoods')->first();
        $oGoods = $order->orderGoods;
        $fan_id = Token::getUid();
        DB::beginTransaction();
        try {
            Order::where('id', $list['id'])->update(['use_state' => -2]);
            //Fans表 积分处理
            Fan::where('id', Token::getUid())->decrement('point', $order->integral);
            //商品数量
            foreach ($oGoods as $oGood) {
                MallGood::where([['id', $oGood->good_id], ['up_id', $oGood->up_id]])->increment('stock', $oGood->num);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }


}

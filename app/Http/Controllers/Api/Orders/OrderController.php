<?php

namespace App\Http\Controllers\Api\Orders;

use App\Models\Activity;
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
use App\Services\Token;
use App\Utils\Parameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Param;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{
//    获取商城订单
    public function getMallOrder()
    {
        $orders = Order::where('type', Parameter::mall)->orderBy('created_at', 'desc')
            ->with(['goods' => function ($query) {
                $query->with('imgs');
            }])->with('setting')->paginate(20);
        return response()->json(['data' => $orders]);
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

//   获取申请退款订单
    public function getRefundOrder()
    {
        $order = Order::where('use_state', -1)->orderBy('created_at', 'desc')->get();
        $mall = $order->where('type', Parameter::general)->orWhere('type', Parameter::discount)->orWhere('tpye', Parameter::member)->get();
        $join = $order->where('type', Parameter::join)->get();
        $active = $order->where('type', Parameter::active)->get();
        return response()->json(['$mall' => $mall, 'join' => $join, 'active' => $active]);
    }

//    获取用户所有订单
    public function getFanOrder()
    {
        $fan_id = Token::getUid();
        $orders = Order::where('fan_id', $fan_id)->orderBy('created_at', 'desc')
            ->with(['goods' => function ($query) {
                $query->with('imgs');
            }])
            ->with('orderGoods')
            ->with('setting')->paginate(20);
        return response()->json(['data' => $orders]);
    }

//      获取单笔订单
    public function show()
    {
        $id = request()->order;
        $order = Order::where('id', $id)
            ->with(['goods' => function ($query) {
                $query->with('imgs');
            }])
            ->with('orderGoods')
            ->with('setting')->get();
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

            if ($good->type == Parameter::member && $member == null) {
                $good->error = 1; //用户非会员 存在会员商品
            } else {
                $good->error = 0; //无错误
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
        }
        return $result;
    }

    public function orderMall($ps, array $rGoods)
    {
        $type = Parameter::mall;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-商城商品';
        $orderSetting = OrderSetting::orderBy('created_at', 'desc')->first(); //订单截止日
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

        $goods = MallGood::whereIn('id', $rgIDs)->get();

        foreach ($rGoods as $rGood) {
            $good = $goods->where('id', $rGood['id'])->first();
            $gDiscount = $good->discount;
            $gPrice = $good->price;
            $num = $rGood['num'];
            //验证
            if ($good->type == Parameter::member && $member == null) {
                $good->error = 1; //用户非会员 存在会员商品
                $is_error = 1;

            } else {
                $good->error = 0; //无错误
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

            if ($good->type == Parameter::group) {
                $groups = MallGoodGroup::where([
                    ['fan_id', $fan_id], ['good_id', $good->id], ['is_effect', 1]
                ])->get();
                if (count($groups) > o) {
                    $good->error = 6; //已参与该团购
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
//            订单号
        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'ps' => $ps, 'order_no' => $order_no, 'body' => $body, 'end_id' => $orderSetting->id, 'discount_type' => $discount_type, 'discount' => $pDiscount]);
            foreach ($rGoods as $rGood) {
                $good = $goods->where('id', $rGood['id'])->first();
                OrderGood::create(['type' => $type, 'order_id' => $order->id, 'good_id' => $rGood['id'], 'num' => $rGood['num'], 'price' => $good->price, 'discount' => $good->discount]);
                MallGood::where('id', $rGood['id'])->update(['stock' => $good->stock - $rGood['num']]);
            }
            DB::commit();
            return response()->json(['status' => 1, 'msg' => '新增成功！','id'=>$order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }


//            if ($good->type == Parameter::general) {
//                $gPrice = $good->price;
//                $num = $rGood['num'];
//                $genealPrice = $genealPrice + ($gPrice * $num);
//            } else {
//                //除一般商品外价钱总和
//                $gPrice = $good->price;
//                $num = $rGood['num'];
//                $price = $price + ($gPrice * $num);
//            }

//            会员处理 会员 满减
//        if ($member && $genealPrice > 0) {
//            $memberSet = MemberSetting::first();
//            $offer_status = $memberSet->offer_status;
//            $offers = json_decode($memberSet->offer);
//            if ($offer_status == 1) {
////                  满减 从小到大
//                $count = count($offers);
//                for ($i = 0; $i < $count; $i++) {
//                    if ($i + 1 < $count) {
//                        if ($genealPrice >= $offers[$i]->condition && $genealPrice < $offers[$i + 1]->condition) {
//                            $discount = $offers[$i]->discount;
//                            break;
//                        }
//                    } else {
//                        $discount = $offers[$i]->discount;
//                    }
//                }
//                $genealPrice = $genealPrice - $discount;
//                $discount_type = 1;
//                $pDiscount = $discount;
//            } else if ($offer_status == 2) {
////                  折扣
//                $discount = $offers[0]->discount;
//                $discount_type = 2;
//                $pDiscount = $genealPrice - sprintf("%.2f", $genealPrice * $discount);
//                $genealPrice = sprintf("%.2f", $genealPrice * $discount);
//            }
//        }

//        if ($genealPrice > 0) {
//            $price = $price + $genealPrice;
//        }
    }


    public function orderGeneral($ps, array $rGoods)
    {
        $type = Parameter::general;
        $fan_id = Token::getUid();
        $body = Parameter::body_CO . '-商城商品';
        $orderSetting = OrderSetting::orderBy('created_at', 'desc')->first();
        $price = 0;
        $discount_type = 0;
        $pDiscount = 0;
        $rgIDs = [];

        foreach ($rGoods as $rGood) {
            array_push($rgIDs, $rGood['id']);
        }
        $goods = MallGood::whereIn('id', $rgIDs)->get();
        foreach ($rGoods as $rGood) {

            $good = $goods->where('id', $rGood['id'])->first();
            if ($good->is_up == 0) {
                return response()->json(['state' => 'error', 'message' => '存在已下架商品']);
            }
            $gPrice = $good->price;
            $num = $rGood['num'];
            $price = $price + ($gPrice * $num);

        }
//            会员处理 会员 满减的
        $member = Member::find($fan_id);
        if ($member) {
            $memberSet = MemberSetting::first();
            $offer_status = $memberSet->offer_status;
            $offers = json_decode($memberSet->offer);
            if ($offer_status == 1) {
//                  满减 从小到大
                $count = count($offers);
                for ($i = 0; $i < $count; $i++) {
                    if ($i + 1 < $count) {
                        if ($price >= $offers[$i]->condition && $price < $offers[$i + 1]->condition) {
                            $discount = $offers[$i]->discount;
                            break;
                        }
                    } else {
                        $discount = $offers[$i]->discount;
                    }
                }
                $price = $price - $discount;
                $discount_type = 1;
                $pDiscount = $discount;
            } else if ($offer_status == 2) {
//                  折扣
                $discount = $offers[0]->discount;
                $discount_type = 2;
                $pDiscount = $price - sprintf("%.2f", $price * $discount);
                $price = sprintf("%.2f", $price * $discount);
            }
        }
//            订单号
        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'ps' => $ps, 'order_no' => $order_no, 'body' => $body, 'end_id' => $orderSetting->id, 'discount_type' => $discount_type, 'discount' => $pDiscount]);
            foreach ($rGoods as $rGood) {
                $good = $goods->where('id', $rGood['id'])->first();
                OrderGood::create(['type' => $type, 'order_id' => $order->id, 'good_id' => $rGood['id'], 'num' => $rGood['num'], 'price' => $good->price, 'discount' => $good->discount]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
    }

    public function orderDiscount($type, $ps, $good_id, $num)
    {
        $fan_id = Token::getUid();
        $orderSetting = OrderSetting::orderBy('created_at', 'desc')->first();
        $body = Parameter::body_CO . '-商城商品';
        $good = MallGood::find($good_id);

        if ($good->is_up == 0) {
            return response()->json(['state' => 'error', 'message' => '商品已下架']);
        }
        if ($good->end_date != '') {
            $end_date = Carbon::parse($good->end_date);
            if ($end_date->lt(Carbon::now())) {
                return response()->json(['state' => 'error', 'message' => '商品优惠已过期']);
            }
        }
        $gPrice = $good->discount;
        $price = $gPrice * $num;

        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;
        DB::beginTransaction();
        try {
            $order = Order::create(['type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'ps' => $ps, 'order_no' => $order_no, 'body' => $body, 'end_id' => $orderSetting->id]);

            OrderGood::create(['type' => $type, 'order_id' => $order->id, 'good_id' => $good_id, 'num' => $num, 'price' => $price, 'discount' => $good->discount]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);
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

    public function payment(int $order_id, int $pay_state, $paytime, $trans_no)
    {
//        成功支付
        if ($pay_state == 1) {
            $order = Order::find($order_id);
            $type = $order->type;
            $use_no = $order_no . mt_rand(0, 9) . mt_rand(0, 9);

            $mallSetting = MallSetting::where('type', $type)->first();
            $switch = $mallSetting->switch;
            if ($switch == 1) {
                $radio = $mallSetting->radio;
                $price = $order->price;
                $integral = round($price * $radio);
            } else {
                $integral = 0;
            }
        } else if ($pay_state == 0) {

        }

        DB::beginTransaction();
        try {
            Order::where('id', $re['id'])->update($re);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '新增成功！']);

    }


}

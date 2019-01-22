<?php

namespace App\Http\Controllers\Api\Malls;

use App\Models\MallGood;
use App\models\MallGoodGroup;
use App\Models\MallSetting;
use App\Models\Order;
use App\Models\OrderGood;
use App\Models\OrderSetting;
use App\Services\Token;
use App\Utils\Parameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MallGoodGroupController extends Controller
{
//    支付 退款两种情况 积分 展示
    //开团
    public function store()
    {
        $list = request(['good_id', 'num']);
        $fan_id = Token::getUid();
        $good = MallGood::find($list['good_id']);
//        验证
        if ($good->type != Parameter::group) {
            return response()->json(['status' => 1, 'error' => 1, 'msg' => '商品类型不为团购']);
        }
        if ($good->limit != 0) {
            $orderGoods = OrderGood::where([
                ['fan_id', $fan_id], ['up_id', $good->up_id]
            ])->get();
            if (count($orderGoods) > $good->limit) {
                return response()->json(['status' => 1, 'error' => 2, 'msg' => '商品已达到购买上限']);
            }
        }
        if ($good->limit != 0 && $list['num'] > $good->limit) {
            return response()->json(['status' => 1, 'error' => 3, 'msg' => '商品数量超出商品上限']);
        }
        if ($list['num'] > $good->stock) {
            return response()->json(['status' => 1, 'error' => 4, 'msg' => '商品大于库存']);
        }
        if ($good->stock == 0) {
            return response()->json(['status' => 1, 'error' => 5, 'msg' => '商品已售罄']);
        }
        if ($good->is_up == 0) {
            return response()->json(['status' => 1, 'error' => 6, 'msg' => '商品已下架']);
        }
        $type = Parameter::mall;
        $body = Parameter::body_CO . '-商城商品';
        $price = $good->discount * $list['num'];
        // 订单号
        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;
        DB::beginTransaction();
        try {
            $order = Order::create([
                'type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'order_no' => $order_no,
                'body' => $body, 'discount_type' => 0, 'discount' => 0
            ]);
            OrderGood::create([
                'type' => $good->type, 'order_id' => $order->id, 'good_id' => $list['good_id'], 'num' => $list['num'],
                'price' => $good->price, 'discount' => $good->discount, 'fan_id' => $fan_id, 'up_id' => $good->up_id
            ]);
            MallGood::where('id', $list['good_id'])->update(['stock' => $good->stock - $list['num']]);
            DB::commit();
            return response()->json(['status' => 1, 'msg' => '新增成功！', 'id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
    }

//    跟团
    public function add()
    {
        $fan_id = Token::getUid();
        $list = request(['good_id', 'num', 'head_id']);
        $good = MallGood::find($list['good_id']);
        $group = MallGoodGroup::find($list['head_id']);
        $gourp_count = MallGoodGroup::where('id', $list['head_id'])->orWhere('head_id', $list['head_id'])->count();
//        验证

        if ($group->state == 1) {
            return response()->json(['status' => 1, 'error' => 7, 'msg' => '团购已完成']);
        }
        if ($group->state == -1) {
            return response()->json(['status' => 1, 'error' => 8, 'msg' => '团购时间已结束']);
        }
        if ($good->group_num == $gourp_count) {
            return response()->json(['status' => 1, 'error' => 9, 'msg' => '团购已达到人数要求']);
        }
        if ($good->type != Parameter::group) {
            return response()->json(['status' => 1, 'error' => 1, 'msg' => '商品类型不为团购']);
        }
        if ($good->limit != 0) {
            $orderGoods = OrderGood::where([
                ['fan_id', $fan_id], ['up_id', $good->up_id]
            ])->get();
            if (count($orderGoods) > $good->limit) {
                return response()->json(['status' => 1, 'error' => 2, 'msg' => '商品已达到购买上限']);
            }
        }
        if ($good->limit != 0 && $list['num'] > $good->limit) {
            return response()->json(['status' => 1, 'error' => 3, 'msg' => '商品数量超出商品上限']);
        }
        if ($list['num'] > $good->stock) {
            return response()->json(['status' => 1, 'error' => 4, 'msg' => '商品大于库存']);
        }
        if ($good->stock == 0) {
            return response()->json(['status' => 1, 'error' => 5, 'msg' => '商品已售罄']);
        }
        if ($good->is_up == 0) {
            return response()->json(['status' => 1, 'error' => 6, 'msg' => '商品已下架']);
        }

        $type = Parameter::mall;
        $body = Parameter::body_CO . '-商城商品';
        $price = $good->discount * $list['num'];
        // 订单号
        $date = Carbon::now()->format('Ymdhi');
        $oNum = sprintf("%04d", Order::where('order_no', 'like', $date . '%')->count() + 1);
        $order_no = $date . $oNum;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'type' => $type, 'fan_id' => $fan_id, 'price' => $price, 'order_no' => $order_no,
                'body' => $body, 'discount_type' => 0, 'discount' => 0
            ]);
            OrderGood::create([
                'type' => $good->type, 'order_id' => $order->id, 'good_id' => $list['good_id'], 'num' => $list['num'],
                'price' => $good->price, 'discount' => $good->discount, 'fan_id' => $fan_id, 'up_id' => $good->up_id
            ]);
            MallGood::where('id', $list['good_id'])->update(['stock' => $good->stock - $list['num']]);
            DB::commit();
            return response()->json(['status' => 1, 'msg' => '新增成功！', 'id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '新增失败' . $e]);
        }
    }
//    public function storeSucess()
//    {
//        $list = request(['id','paytime','trans_no']);
//        $fan_id = Token::getUid();
//        $order = Order::where('id',$list['id'])->with('orderGoods')->first();
//        $orderGoods = $order->orderGoods;
//        $good_id = $orderGoods[0]->good_id;
//        $rand = $this->randomkeys(4);
//        $use_no = $order->order_no . $rand;
//        $integral = 0;
//        $order->use_no = $use_no;
//        // 积分处理
//        $mallSetting = MallSetting::first();
//        if ($mallSetting) {
//            $switch = $mallSetting->switch;
//            if ($switch == 1) {
//                $radio = $mallSetting->radio;
//                $price = $order->price;
//                $integral = round($price * $radio); //积分
//            }
//        }
//        $order->integral = $integral;
//        $order->pay_time = $list['paytime'];
//        $order->trans_no = $list['trans_no'];
//        //截止日
//        $orderSettings = OrderSetting::where('switch', 1)->first();
//        $order->end_id = $orderSettings->id;
//        if ($orderSettings->type = 'date') {
//            $order->end_date = $orderSettings->date();
//        } else {
//            $day = $orderSettings->day();
//            $order->end_date = Carbon::now()->addDays($day);
//        }
//        DB::beginTransaction();
//        try {
//            Order::where('id', $list['id'])->update($order->toArray());
////            QrCode::format('png')->size(200)->generate($use_no, public_path('storage/qrcodes/' . $list['id'] . '.png'));
//            MallGoodGroup::create([
//                'fan_id' => $fan_id, 'good_id' => $good_id, 'head_id' => 0, 'order_id' => $order->id
//            ]);
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
//        }
//        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
//    }

//    public function addSucess()
//    {
//        $list = request(['id','paytime','trans_no']);
//        $fan_id = Token::getUid();
//        $order = Order::where('id',$list['id'])->with('orderGoods')->first();
//        $orderGoods = $order->orderGoods;
//        $good_id = $orderGoods[0]->good_id;
//        $good = MallGood::find($good_id);
//        $rand = $this->randomkeys(4);
//        $use_no = $order->order_no . $rand;
//        $integral = 0;
//        $order->use_no = $use_no;
//        // 积分处理
//        $mallSetting = MallSetting::first();
//        if ($mallSetting) {
//            $switch = $mallSetting->switch;
//            if ($switch == 1) {
//                $radio = $mallSetting->radio;
//                $price = $order->price;
//                $integral = round($price * $radio); //积分
//            }
//        }
//        $order->integral = $integral;
//        $order->pay_time = $list['paytime'];
//        $order->trans_no = $list['trans_no'];
//        //截止日
//        $orderSettings = OrderSetting::where('switch', 1)->first();
//        $order->end_id = $orderSettings->id;
//        if ($orderSettings->type = 'date') {
//            $order->end_date = $orderSettings->date();
//        } else {
//            $day = $orderSettings->day();
//            $order->end_date = Carbon::now()->addDays($day);
//        }
//        $gourp_count = MallGoodGroup::where('id', $list['head_id'])->orWhere('head_id', $list['head_id'])->count();
//        $state = 0;
//        if ($good->group_num == $gourp_count + 1) {
//            $state = 1;
//        }
//        DB::beginTransaction();
//        try {
//            Order::where('id', $list['id'])->update($order->toArray());
////            QrCode::format('png')->size(200)->generate($use_no, public_path('storage/qrcodes/' . $list['id'] . '.png'));
//            MallGoodGroup::create([
//                'fan_id' => $fan_id, 'good_id' => $good_id, 'head_id' => 0, 'order_id' => $order->id,'state'=>$state
//            ]);
//            DB::commit();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
//        }
//        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
//    }

    public function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz 
               ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

//    开团支付成功
    public function storeSucess()
    {
        $list = request(['id', 'paytime', 'trans_no']);
        $fan_id = Token::getUid();
        $order = Order::where('id', $list['id'])->with('orderGoods')->first();
        $orderGoods = $order->orderGoods;
        $good_id = $orderGoods[0]->good_id;
        $order->pay_time = $list['paytime'];
        $order->trans_no = $list['trans_no'];
        $order->pay_state = 1;
        DB::beginTransaction();
        try {
            Order::where('id', $list['id'])->update($order->toArray());
            MallGoodGroup::create([
                'fan_id' => $fan_id, 'good_id' => $good_id, 'head_id' => 0, 'order_id' => $order->id, 'is_effect' => 0
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

//    跟团支付成功
    public function addSucess()
    {
        $list = request(['id', 'paytime', 'trans_no']);
        $fan_id = Token::getUid();
        $order = Order::where('id', $list['id'])->with('orderGoods')->first();
        $orderGoods = $order->orderGoods;
        $good_id = $orderGoods[0]->good_id;
        $good = MallGood::find($good_id);
        $order->pay_time = $list['paytime'];
        $order->trans_no = $list['trans_no'];

        $gourp_count = MallGoodGroup::where('id', $list['head_id'])->orWhere('head_id', $list['head_id'])->count();
        $state = 0;
        if ($good->group_num == $gourp_count + 1) {
            $state = 1;
        }
        DB::beginTransaction();
        try {
            Order::where('id', $list['id'])->update($order->toArray());
            MallGoodGroup::create([
                'fan_id' => $fan_id, 'good_id' => $good_id, 'head_id' => 0, 'order_id' => $order->id, 'is_effect' => $state
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'msg' => '修改失败' . $e]);
        }
        return response()->json(['status' => 'success', 'msg' => '修改成功！']);
    }

//    展示
    public function getGroup()
    {
//        团购详情
        $order_id = request('order_id');
        $group = MallGoodGroup::where('order_id', $order_id)->with('childGroups')->first();
        if ($group->head_id != 0) {
            $group = MallGoodGroup::where('id',$group->head_id)->with('childGroups')->first();
        }
        return response()->json(['data' => $group]);
//        更改状态

    }


}

<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/12/19
 * Time: 11:09
 */

namespace App\Http\Controllers\Api\Lotteries;


use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponRecord;
use App\Models\FanLottery;
use App\Models\LotteryHistory;
use App\Models\LotteryPrize;
use App\Services\Token;

class PrizeController extends Controller
{
    public function index()
    {
        $date=LotteryPrize::where('activity_id',request()->activity)->with('coupon')->get();
        return response()->json(['status' => 'success', 'data' =>$date]);
    }

    public function store()
    {
        $data=request()->all();
        $data['lottery_number']=null;
        if($data['number']!=0){
            $data['lottery_number']=$data['number'];
        }
        if(LotteryPrize::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        $data['lottery_number']=null;
        if($data['number']!=0){
            $data['lottery_number']=$data['number'];
        }
        if(LotteryPrize::where('id', request()->prize)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(LotteryPrize::where('id', request()->prize)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function getPrizes($activity_id){
        $prizes=LotteryPrize::where('activity_id',$activity_id)
            ->select('id', 'probably','lottery_number')->get();
        if(count($prizes)==0){
            return $prizes;
        }
        $noProbably=100;
        foreach ($prizes as $prize){
            $noProbably=$noProbably-$prize['probably'];
            if($prize->lottery_number=='0'){
                $prize->probably=0;
            }
        }
        $prizes=$prizes->toArray();
        if($noProbably!='0'){
            $noPrize=array('id'=>'no','probably'=>$noProbably);
            array_push($prizes,$noPrize);
        }
        return $prizes;
    }

    public function result()
    {
        $fan_id=Token::getUid();
        $activity_id=request()->activity_id;
        $prizes=$this->getPrizes($activity_id);
        $fan_lottery_data=FanLottery::whereFan_id($fan_id)->whereActivity_id($activity_id)->first();
        $chance_number=$fan_lottery_data->number;//可抽奖次数
        foreach ($prizes as $key => $val) {
            $arr[$val['id']] = $val['probably'];
        }
        $rid = $this->getRand($arr); //根据概率获取奖项id
        if($rid!='no'){
            //处理奖品溢出
            $un_prize=LotteryPrize::find($rid);
            $lottery_number=$un_prize->lottery_number-1;
            if ($un_prize->number!=0){
                if($lottery_number<0){
                    $rid='no';
                }
                LotteryPrize::whereId($rid)->update(['lottery_number'=>$lottery_number]);
            }
        }
        //奖品放入卡包，记录
        $result_prize=LotteryPrize::find($rid);
        if($result_prize){
            $coupon=Coupon::find($result_prize->coupon_id);
            if($coupon){
                $time=Coupon::getTime($result_prize->coupon_id);
                $save_coupon=CouponRecord::create(['fan_id'=>$fan_id,'coupon_id'=>$result_prize->coupon_id,'status'=>'0',
                    'start_time'=> $time['start'],'end_time'=>$time['end']]);
                $save_history=LotteryHistory::create(['fan_id'=>$fan_id,
                    'activity_id'=>$activity_id,'coupon_id'=>$result_prize->coupon_id,'coupon_name'=>$coupon->name]);
            }
        }else{
            $result_prize='noprize';
        }
        $chance_number=(int)$chance_number-1;
        //修改用户可抽奖次数
        $save_number=FanLottery::where('fan_id',$fan_id)->where('activity_id',$activity_id)
                        ->update(['number'=>$chance_number]);
        return response()->json(["status"=>"success","data"=>compact('result_prize','chance_number')]);
    }

    public function getRand($proArr) {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}
<?php

namespace App\Http\Controllers\Api\Fans;

use App\Http\Requests\SignInRequest;
use App\Models\Fan;
use App\Models\PointHistory;
use App\Models\Sign;
use App\Models\SignHistory;
use App\Models\SignTask;
use App\Services\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SignInController extends Controller
{

    public function index()
    {
        $sign_tasks=SignTask::all();
        return response()->json(['status' => 'success', 'data' => $sign_tasks]);
    }

    public function store(SignInRequest $request)
    {
        $data=request()->all();
        if(SignTask::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update(SignInRequest $request)
    {
        $data = request()->all();
        if(SignTask::where('id', request()->sign_task)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(SignTask::where('id', request()->sign_task)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function get_sign()
    {
        $fan_id=Token::getUid();
        $sign_data=Sign::where('fan_id',$fan_id)->with('fan:id,point')->first();
        if(count($sign_data)>0){
            $now=Carbon::parse()->toDateString();
            if($now==$sign_data->last_day){
                $sign_data->signButtonFlag=false;
            }else{
                $sign_data->signButtonFlag=true;
            }
        }
        return response()->json(['status' => 'success', 'data' =>$sign_data ]);
    }

    public function signIn()
    {
        $data['fan_id']=Token::getUid();
        $data['last_day']=Carbon::now()->toDateString();
        $sign_data=Sign::where('fan_id',$data['fan_id'])->first();
        if(count($sign_data)==0){
            Sign::create(['fan_id'=>$data['fan_id']]);
            $sign_data=Sign::where('fan_id',$data['fan_id'])->first();
        }
        $date=Carbon::parse($sign_data->last_day)->modify('+1 days')->toDateString();
        if($date==$data['last_day']){//连续签到
            $data['continuity_day']=$sign_data->continuity_day+1;
            $data['task_day']=$sign_data->task_day+1;
            $reward_day=SignTask::all()->count();//连续签到了最后一天奖励，重新第一天开始
            if($data['task_day']>$reward_day){
                $data['task_day']=1;
            }
        }else if($sign_data->last_day==Carbon::parse()->toDateString()){
            return response()->json(['status' => 'error', 'msg' => '更新失败！']);
        }else{//断签 从第一天重新开始
            $data['continuity_day']=1;
            $data['task_day']=1;
        }
        $reward_data=SignTask::where('day',$data['task_day'])->first();
        switch ($reward_data->type){
            case 'point':
                if($reward_data->method==2){
                    $rand=explode("-",$reward_data->reward);
                    $add_point=mt_rand($rand[0],$rand[1]);
                }else{
                    $add_point=$reward_data->reward;
                }
                $get_reward="+".$add_point."积分";
                $fan_data=Fan::find($data['fan_id']);
                $point=$fan_data->point+$add_point;
                $fan_data->update(['point'=>$point]);
                PointHistory::create(['fan_id'=>$data['fan_id'],'state'=>'1',
                    'point'=>$add_point,'tag'=>'signIn',
                    'comment'=>$data['last_day'].'签到所得']);
            break;
        }
        $save=Sign::where('fan_id',$data['fan_id'])->update($data);
        if($save){
            $sign_histories=SignHistory::create(['fan_id'=>$data['fan_id'],
                'sign_day'=> $data['last_day'],'content'=>$get_reward]);
            $new_sign=Sign::where('fan_id',$data['fan_id'])->with('fan:id,point')->first();
            return response()->json(['status' => 'success',
                'data' =>compact('new_sign','add_point') ]);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

}

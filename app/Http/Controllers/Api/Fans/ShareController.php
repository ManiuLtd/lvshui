<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/16
 * Time: 16:03
 */

namespace App\Http\Controllers\Api\Fans;


use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponRecord;
use App\Models\Fan;
use App\Models\ShareOver;
use App\Models\ShareRecords;
use App\Models\ShareTask;
use App\Services\TemplateNotice;

class ShareController extends Controller
{
    public function index()
    {
        $date=ShareTask::first();
        return response()->json(['status' => 'error', 'data' =>$date]);
    }


    public function store()
    {
        $data=request()->all();
        if(ShareTask::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if(ShareTask::where('id', request()->task)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(ShareTask::where('id', request()->task)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function share()
    {
        $share_id=request()->share_id;
        $fan=Fan::find($share_id);
        $beshare_id=request()->beshare_id;
        $save=ShareRecords::create(['share_id'=>$share_id,'beshare_id'=>$beshare_id]);
        $share_count=ShareRecords::where('share_id',$share_id)->count();
        $task=ShareTask::first();
        if($task->task_target==$share_count){
            //任务完成
              $template=new  TemplateNotice();
              $array=['first'=>'您已完成分享活动！',
                  'key1'=>$task->name,
                  'key2'=>$fan->nickname,
                  'key3'=>'请前往活动页面填写联系方式'];
              $template->sendNotice('oLOcY0jf0SLhG_LN27yU0FIZJWUo',
              'jtOZ0m2YaKn3-6AhOlWlFMtED4Cda46rILl-E-Kqf2o',
              'www.baidu.com',
                  $array);
            $time = Coupon::getTime($task->	reward);
//            $save_coupon=CouponRecord::create(['fan_id'=>$share_id,'coupon_id'=>$task->	reward,'status'=>'0',
//                'start_time'=> $time['start'],'end_time'=>$time['end']]);
            return response()->json(['status' => 'success', 'msg' => '任务完成！']);
        }
        return response()->json(['status' => 'success', 'msg' => '任务已完成！']);
    }

    public function showRegister()
    {
        $value=request()->value;
        if(isset($value)){
            $data=ShareOver::orWhere('name','like','%'.$value.'%')->orWhere('contact_way','like','%'.$value.'%')->get();
        }else{
            $data=ShareOver::all();
        }
        return response()->json(['status' => 'success', 'data' =>$data]);
    }

    public function register()
    {
        $data=request()->all();
        if(ShareOver::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function checkRegister()
    {
        $fan_id=request()->fan_id;
        if(ShareOver::where('fan_id',$fan_id)->update(['flag'=>'1'])) {
            return response()->json(['status' => 'success', 'msg' => '修改成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '修改失败！']);
    }

    public function beShareShow()
    {
        $share_id=request()->share_id;
        $beshare_id=request()->beshare_id;
        $task=ShareTask::first();
        if($task->status==0){
            $flag='noopen';
            return response()->json(['status' => 'success', 'data' =>compact('flag')]);
        }
        //被分享者看到的页面，beshaer表示关注，beshaerover表示分享者的任务完成
        $flag='beshaer';
        $share_record=ShareRecords::where('share_id',$share_id)->where('beshare_id',$beshare_id)->get();
        $share=ShareRecords::where('share_id',$share_id)->with('share:id,nickname,headimgurl')
            ->with('beshaer:id,nickname,headimgurl')->get();
        if($share_record->count()>0){
            //已经帮助过
            $flag='have';
        }
        if($share->count()==$task->task_target	){
            $flag='over';
        }
        return response()->json(['status' => 'success', 'data' =>compact('flag','share')]);
    }

    public function shareShow()
    {
        $share_id=request()->share_id;
        $task=ShareTask::first();
        if($task->status==0){
            $flag='noopen';
            return response()->json(['status' => 'success', 'data' =>compact('flag')]);
        }
        $share=ShareRecords::where('share_id',$share_id)->with('share:id,nickname,headimgurl')
            ->with('beshaer:id,nickname,headimgurl')->get();
        //分享者看到的页面，shaer表示未完成分享任务显示我要分享按钮，record表示分享者的任务完成显填写个人资料,over任务彻底完成
        $flag='share';
        $share_over='';
        if($share->count()==$task->task_target	){
            //任务完成
            $flag='record';
            $share_over=ShareOver::find($share_id);
            if($share_over){
                $flag='over';
            }
        }
        return response()->json(['status' => 'success', 'data' =>compact('flag','share','share_over')]);
    }
}
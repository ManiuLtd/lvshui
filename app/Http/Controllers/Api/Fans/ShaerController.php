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
use App\Models\ShareRecords;
use App\Models\ShareTask;

class ShaerController extends Controller
{
    public function index()
    {

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
        $beshare_id=request()->beshare_id;
        $save=ShareRecords::create(['share_id'=>$share_id,'beshare_id'=>$beshare_id]);
        $share_count=ShareRecords::where('share_id',$share_id)->count();
        $task=ShareTask::where('task',$share_count)->first();
        $time = Coupon::getTime($task->	reward);
        $save_coupon=Coupon::create(['fan_id'=>$share_id,'coupon_id'=>$task->	reward,'status'=>'0',
                    'start_time'=> $time['start'],'end_time'=>$time['end']]);
        return response()->json(['status' => 'success', 'msg' => '更新成功！']);
    }
}
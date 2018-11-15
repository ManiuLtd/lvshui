<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/10
 * Time: 10:23
 */

namespace App\Http\Controllers\Api\Activities;


use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityController extends  Controller
{
    public function index()
    {
        $activitys=Activity::orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $activitys]);
    }

    public function show()
    {
        $activity=Activity::with('fans')->withCount('fans')->find(request()->activity);
        $activity_sign_count=$activity->fans_count;
        return response()->json(['status' => 'success', 'data' => $activity]);
    }

    public function store(ActivityRequest $request)
    {
        $data = request()->all();
        $activity=Activity::create($data);
        if($activity){
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update(ActivityRequest $request)
    {
        $data = request()->all();

        if(Activity::find(request()->activity)->update($data)){
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(Activity::find(request()->activity)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }


    public function time_task()
    {
        $today=Carbon::parse()->toDateString();
        $updte1=Activity::where('sign_start_time',$today)->update(['status'=>'1']);
        $updte2=Activity::where('end_time',$today)->update(['status'=>'-1']);
        return true;
    }
}
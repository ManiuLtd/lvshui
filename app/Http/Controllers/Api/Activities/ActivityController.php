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

class ActivityController extends  Controller
{
    public function index()
    {
        $activitys=Activity::orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $activitys]);
    }

    public function store(ActivityRequest $request)
    {
        $data = request()->all();
//        if(isset(request()->sign_time)&&isset(request()->activity_time)){
//            $sign_time = explode('-',request('sign_time'));
//            $data['sign_start_time'] =trim($sign_time[0]);
//            $data['sign_end_time'] =trim($sign_time[1]);
//
//            $activity_time = explode('-',request('activity_time'));
//            $data['start_time'] =trim($activity_time[0]);
//            $data['end_time'] =trim($activity_time[1]);
//
//        }
        $activity=Activity::create($data);
        if($activity){
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update(ActivityRequest $request)
    {
        $data = requestrequest()->all();
//        if(isset(request()->sign_time)&&isset(request()->activity_time)){
//            $sign_time = explode('-',request('sign_time'));
//            $data['sign_start_time'] =trim($sign_time[0]);
//            $data['sign_end_time'] =trim($sign_time[1]);
//
//            $activity_time = explode('-',request('activity_time'));
//            $data['start_time'] =trim($activity_time[0]);
//            $data['end_time'] =trim($activity_time[1]);
//
//        }
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

    public function wx_show()
    {
        $activitys=Activity::where('status','1')->orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $activitys]);
    }

}
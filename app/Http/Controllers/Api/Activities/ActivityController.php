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

        $activity=Activity::create($request->all());
        if($activity){
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update(ActivityRequest $request)
    {
        $request=request()->all();
        if(Activity::find(request()->activity)->update($request)){
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
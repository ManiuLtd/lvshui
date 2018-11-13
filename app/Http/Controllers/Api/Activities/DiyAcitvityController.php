<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/13
 * Time: 14:43
 */

namespace App\Http\Controllers\Api\Activities;


use App\Http\Controllers\Controller;
use App\Models\DivActivity;

class DiyAcitvityController extends Controller
{
    public function index()
    {
        $activitys=DivActivity::orderBy('created_at','desc')->paginate(20);
        return response()->json(['status' => 'success', 'data' => $activitys]);
    }

    public function store()
    {
        $data = request()->all();
        $activity=DivActivity::create($data);
        if($activity){
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if(DivActivity::find(request()->divactivity)->update($data)){
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(DivActivity::find(request()->divactivity)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }
}
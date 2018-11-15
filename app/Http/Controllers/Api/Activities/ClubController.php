<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:46
 */

namespace App\Http\Controllers\Api\Activities;


use App\Http\Controllers\Controller;
use App\Models\Club;

class ClubController extends Controller
{
    public function index()
    {

    }

    public function store()
    {
        $data= request()->all();
        $club=Club::create($data);
        if($club){
            return response()->json(['status' => 'success', 'msg' => '新增成功!']);
        }
            return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if(Club::find(request()->club)->update($data)){
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(Club::find(request()->club)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function join(Club $club)
    {
        $fan_id='';
        $sign=$club->fans()->attach($fan_id);
        return response()->json(['status' => 'success', 'msg' => '更新成功！']);
    }
}
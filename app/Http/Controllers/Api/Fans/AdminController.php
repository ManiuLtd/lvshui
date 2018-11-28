<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/28
 * Time: 16:57
 */

namespace App\Http\Controllers\Api\Fans;


use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Fan;

class AdminController extends Controller
{
    public function index()
    {
        $data=Fan::with('admin')->get();
        return response()->json(['status' => 'success', 'data' =>$data]);
    }

    public function store()
    {
        $data=request()->all();
        if(Admin::create($data)){
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if(Admin::where('id', request()->admin)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(Admin::where('id', request()->admin)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }
}